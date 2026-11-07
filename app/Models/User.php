<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tenant_id',
        'is_active',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'is_active'             => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    // ── Two-Factor Authentication Helpers ──────────────────────────────────

    public function generateTwoFactorCode(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'two_factor_code'       => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        return $code;
    }

    public function resetTwoFactorCode(): void
    {
        $this->forceFill([
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
        ])->save();
    }

    public function verifyTwoFactorCode(?string $inputCode): bool
    {
        if (empty($inputCode) || empty($this->two_factor_code)) {
            return false;
        }

        if ($this->two_factor_expires_at && now()->isAfter($this->two_factor_expires_at)) {
            return false;
        }

        return hash_equals((string) $this->two_factor_code, trim($inputCode));
    }

    public function twoFactorExpiresInSeconds(): int
    {
        if (! $this->two_factor_expires_at) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->two_factor_expires_at, false));
    }

    // ── Relations ─────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function passkeys(): HasMany
    {
        return $this->hasMany(Passkey::class);
    }

    public function hasPasskeys(): bool
    {
        return $this->passkeys()->exists();
    }

    // ── Role Helpers ──────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isAnalyst(): bool
    {
        return $this->role === 'analyst';
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    public function canImport(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    public function canExport(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    public function canManageUsers(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    public function canManagePointsOfSale(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // ── Display Helpers ───────────────────────────────────────────────────

    public function roleBadge(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin'       => 'Admin',
            'analyst'     => 'Analyste',
            default       => $this->role,
        };
    }

    public function roleColor(): string
    {
        return match ($this->role) {
            'super_admin' => '#FCD34D',
            'admin'       => '#60A5FA',
            'analyst'     => '#34D399',
            default       => '#9CA3AF',
        };
    }
}

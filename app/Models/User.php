<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ── Relations ─────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
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
            'super_admin' => '⭐ Super Admin',
            'admin'       => '🔧 Admin',
            'analyst'     => '👁 Analyste',
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

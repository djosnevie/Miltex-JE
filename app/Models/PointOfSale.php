<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PointOfSale extends Model
{
    protected $table = 'points_of_sale';

    protected $fillable = [
        'company_id',
        'name',
        'location_identifier',
        'city',
        'address',
        'phone',
        'email',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function activeDevices(): HasMany
    {
        return $this->hasMany(Device::class)->where('status', 'active');
    }

    /** All journals from all DEF devices of this POS */
    public function journals(): HasManyThrough
    {
        return $this->hasManyThrough(Journal::class, Device::class);
    }

    /** All invoices from all journals of all DEF devices of this POS */
    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(
            Invoice::class,
            Journal::class,
            'device_id',   // FK on journals pointing to devices
            'journal_id',  // FK on invoices pointing to journals
        );
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // ── Computed Stats ────────────────────────────────────────────────────

    public function deviceCount(): int
    {
        return $this->devices()->count();
    }

    public function journalCount(): int
    {
        return $this->journals()->count();
    }

    public function totalTtc(): float
    {
        return (float) $this->journals()->sum('total_ttc');
    }

    public function totalTva(): float
    {
        return (float) $this->journals()->sum('total_tva');
    }

    public function totalInvoices(): int
    {
        return (int) $this->journals()->sum('total_invoices');
    }

    // ── Display Helpers ───────────────────────────────────────────────────

    public function statusBadge(): string
    {
        return $this->is_active ? '🟢 Actif' : '🔴 Inactif';
    }

    public function fullAddress(): string
    {
        return collect([$this->address, $this->city])->filter()->implode(', ');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'point_of_sale_id',
        'nid',
        'isf',
        'serial_number',
        'model',
        'firmware_version',
        'status',
        'description',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function pointOfSale(): BelongsTo
    {
        return $this->belongsTo(PointOfSale::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForPos($query, int $posId)
    {
        return $query->where('point_of_sale_id', $posId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'active'      => '🟢 Actif',
            'inactive'    => '🔴 Inactif',
            'maintenance' => '🟡 Maintenance',
            default       => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'active'      => '#34D399',
            'inactive'    => '#F87171',
            'maintenance' => '#FCD34D',
            default       => '#9CA3AF',
        };
    }

    public function journalCount(): int
    {
        return $this->journals()->count();
    }

    public function lastJournal(): ?Journal
    {
        return $this->journals()->latest('parsed_at')->first();
    }
}

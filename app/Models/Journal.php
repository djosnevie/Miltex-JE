<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    protected $fillable = [
        'device_id',
        'filename',
        'original_name',
        'file_size',
        'parsed_at',
        'start_date',
        'end_date',
        'total_invoices',
        'total_credits',
        'total_cancelled',
        'total_ttc',
        'total_ht',
        'total_tva',
        'currency',
        'file_hash',
    ];

    protected $casts = [
        'parsed_at'   => 'datetime',
        'start_date'  => 'datetime',
        'end_date'    => 'datetime',
        'total_ttc'   => 'decimal:2',
        'total_ht'    => 'decimal:2',
        'total_tva'   => 'decimal:2',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class);
    }

    // ── Scopes de filtrage rapide ──────────────────────────────────────────

    public function scopeForDevice($query, int $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeBetweenDates($query, string $from, string $to)
    {
        return $query->whereBetween('start_date', [$from, $to]);
    }
}

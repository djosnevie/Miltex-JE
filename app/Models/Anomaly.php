<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anomaly extends Model
{
    protected $fillable = [
        'journal_id',
        'invoice_id',
        'severity',
        'type',
        'description',
        'is_resolved',
        'notes',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

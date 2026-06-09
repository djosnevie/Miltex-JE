<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'journal_id',
        'invoice_no',
        'serial_number',
        'z_number',
        'date_time',
        'buyer_name',
        'buyer_id',
        'buyer_type',
        'vendeur',
        'total_ttc',
        'total_ht',
        'total_tva',
        'currency',
        'type',
        'code_def',
        'compteur_brut',
        'has_mcf_error',
        'mcf_error_message',
        'original_ref_code',
        'payment_mode',
        'raw_text',
    ];

    protected $casts = [
        'date_time'     => 'datetime',
        'total_ttc'     => 'decimal:2',
        'total_ht'      => 'decimal:2',
        'total_tva'     => 'decimal:2',
        'has_mcf_error' => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeCreditNotes($query)
    {
        return $query->where('type', 'credit_note');
    }

    public function scopeCancelled($query)
    {
        return $query->where('type', 'cancelled');
    }

    public function scopeWithMcfError($query)
    {
        return $query->where('has_mcf_error', true);
    }

    public function scopeForPeriod($query, string $from, string $to)
    {
        return $query->whereBetween('date_time', [$from, $to]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public function isSale(): bool     { return $this->type === 'sale'; }
    public function isCredit(): bool   { return $this->type === 'credit_note'; }
    public function isCancelled(): bool{ return $this->type === 'cancelled'; }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_index',
        'name',
        'qty',
        'pu',
        'total',
        'tax_group',
    ];

    protected $casts = [
        'pu'    => 'decimal:2',
        'total' => 'decimal:2',
        'qty'   => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

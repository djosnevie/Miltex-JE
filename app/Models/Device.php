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
        'model',
        'firmware_version',
    ];

    public function pointOfSale(): BelongsTo
    {
        return $this->belongsTo(PointOfSale::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }
}

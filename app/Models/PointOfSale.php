<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointOfSale extends Model
{
    protected $table = 'points_of_sale';

    protected $fillable = [
        'company_id',
        'name',
        'location_identifier',
        'city',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Company extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'nif',
        'rccm',
        'address_street',
        'address_city',
        'email',
        'phone',
        'max_points_of_sale',
    ];

    protected $casts = [
        'max_points_of_sale' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function pointsOfSale(): HasMany
    {
        return $this->hasMany(PointOfSale::class);
    }

    public function devices(): HasManyThrough
    {
        return $this->hasManyThrough(Device::class, PointOfSale::class);
    }

    public function currentPointsOfSaleCount(): int
    {
        return $this->pointsOfSale()->count();
    }

    /**
     * Quota effectif de points de vente (soit propre à la société, soit hérité du tenant).
     */
    public function maxPointsOfSale(): ?int
    {
        if ($this->max_points_of_sale !== null) {
            return $this->max_points_of_sale;
        }

        return $this->tenant?->max_points_of_sale;
    }

    /**
     * Vérifie si la société peut créer un nouveau point de vente.
     */
    public function canCreatePointOfSale(): bool
    {
        $max = $this->maxPointsOfSale();
        if ($max === null || $max <= 0) {
            return true; // Quota illimité
        }

        if ($this->tenant && $this->max_points_of_sale === null) {
            return $this->tenant->canCreatePointOfSale();
        }

        return $this->currentPointsOfSaleCount() < $max;
    }
}

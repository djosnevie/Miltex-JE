<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'subdomain',
        'status',
        'max_points_of_sale',
    ];

    protected $casts = [
        'max_points_of_sale' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function pointsOfSale(): HasManyThrough
    {
        return $this->hasManyThrough(PointOfSale::class, Company::class);
    }

    /**
     * Nombre actuel de points de vente créés pour ce tenant.
     */
    public function currentPointsOfSaleCount(): int
    {
        return $this->pointsOfSale()->count();
    }

    /**
     * Vérifie si le tenant a le droit de créer un point de vente supplémentaire.
     */
    public function canCreatePointOfSale(): bool
    {
        if ($this->max_points_of_sale === null || $this->max_points_of_sale <= 0) {
            return true; // Quota illimité
        }

        return $this->currentPointsOfSaleCount() < $this->max_points_of_sale;
    }

    /**
     * Pourcentage d'utilisation du quota de points de vente.
     */
    public function pointsOfSaleQuotaPercentage(): float
    {
        if ($this->max_points_of_sale === null || $this->max_points_of_sale <= 0) {
            return 0.0;
        }

        return min(100.0, round(($this->currentPointsOfSaleCount() / $this->max_points_of_sale) * 100, 1));
    }
}

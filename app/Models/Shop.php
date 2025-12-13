<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_magasin
 * @property int $id_ville
 * @property string $num_voie_magasin
 * @property string $rue_magasin
 * @property string|null $complement_magasin
 * @property string $nom_magasin
 * @property float|null $latitude
 * @property float|null $longitude
 */
class Shop extends Model
{
    public $timestamps = false;

    protected $table = 'magasin';

    protected $primaryKey = 'id_magasin';

    protected $fillable = [
        'id_magasin',
        'id_ville',
        'num_voie_magasin',
        'rue_magasin',
        'complement_magasin',
        'nom_magasin',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'id_ville', 'id_ville');
    }

    public function getFullAddressAttribute(): string
    {
        return trim($this->num_voie_magasin.' '.$this->rue_magasin);
    }

    public function hasCoordinatesAttribute(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function scopeInCity($query, int $cityId)
    {
        return $query->where('id_ville', $cityId);
    }

    public function toApiFormat(): array
    {
        return [
            'id' => $this->id_magasin,
            'name' => $this->nom_magasin,
            'address' => $this->full_address,
            'complement' => $this->complement_magasin,
            'lat' => $this->latitude,
            'lng' => $this->longitude,
            'isOpen' => true, // À implémenter avec horaires d'ouverture
            'city' => trim($this->city->nom_ville),
            'postalCode' => trim($this->city->cp_ville),
            'country' => $this->city->pays_ville,
        ];
    }
}

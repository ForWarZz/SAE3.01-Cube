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
 * @property-read Ville $ville
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

    /**
     * Relation : Un magasin appartient à une ville
     */
    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'id_ville', 'id_ville');
    }

    /**
     * Accesseur : Adresse complète du magasin
     */
    public function getFullAddressAttribute(): string
    {
        return trim($this->num_voie_magasin . ' ' . $this->rue_magasin);
    }

    /**
     * Accesseur : Vérifie si le magasin a des coordonnées GPS
     */
    public function hasCoordinatesAttribute(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Scope : Magasins avec coordonnées GPS
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /**
     * Scope : Magasins d'une ville spécifique
     */
    public function scopeInCity($query, int $cityId)
    {
        return $query->where('id_ville', $cityId);
    }

    /**
     * Formate le magasin pour l'API JSON
     */
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
            'city' => $this->ville ? trim($this->ville->nom_ville) : null,
            'postalCode' => $this->ville ? trim($this->ville->cp_ville) : null,
            'country' => $this->ville ? $this->ville->pays_ville : null,
        ];
    }
}
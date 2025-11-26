<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_article
 * @property int $id_millesime
 * @property int $id_modele_velo
 * @property int $id_materiau_cadre
 * @property float $prix_article
 * @property int $id_categorie
 * @property string $nom_article
 * @property string $description_article
 * @property string $resumer_article
 */
class Bike extends Model
{
    protected $table = 'velo';
    protected $primaryKey = 'id_article';

    protected $fillable = [
        'id_millesime',
        'id_modele_velo',
        'id_materiau_cadre',
        'prix_article',
        'id_categorie',
        'nom_article',
        'description_article',
        'resumer_article'
    ];

    public function frameMaterial(): BelongsTo
    {
        return $this->belongsTo(BikeFrameMaterial::class, 'id_materiau_cadre', 'id_materiau_cadre');
    }

    public function vintage(): BelongsTo
    {
        return $this->belongsTo(Vintage::class, 'id_millesime', 'id_millesime');
    }

    public function bikeModel(): BelongsTo
    {
        return $this->belongsTo(BikeModel::class, 'id_modele_velo', 'id_modele_velo');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function references(): HasMany
    {
        return $this->hasMany(BikeReference::class, 'id_article', 'id_article');
    }
}

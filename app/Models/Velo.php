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
class Velo extends Model
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

    public function materiauCadre(): BelongsTo
    {
        return $this->belongsTo(MateriauCadreVelo::class, 'id_materiau_cadre', 'id_materiau_cadre');
    }

    public function millesime(): BelongsTo
    {
        return $this->belongsTo(Millesime::class, 'id_millesime', 'id_millesime');
    }

    public function modeleVelo(): BelongsTo
    {
        return $this->belongsTo(ModeleVelo::class, 'id_modele_velo', 'id_modele_velo');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function references(): HasMany
    {
        return $this->hasMany(ReferenceVelo::class, 'id_article', 'id_article');
    }
}

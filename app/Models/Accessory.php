<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id_article
 * @property int $id_reference
 * @property int $id_matiere_accessoire
 * @property float $prix_article
 * @property int $id_categorie
 * @property string $nom_article
 * @property string $description_article
 * @property string $resumer_article
 * @property Article $article
 */
class Accessory extends Model
{
    public const WEIGHT_CHARACTERISTIC_ID = 31;

    protected $table = 'accessoire';

    protected $primaryKey = 'id_article';

    public $timestamps = false;

    protected $fillable = [
        'id_article',
        'id_reference',
        'id_matiere_accessoire',
        'prix_article',
        'id_categorie',
        'nom_article',
        'description_article',
        'resumer_article',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('active_article', function ($builder) {
            $builder->whereHas('article');
        });
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function baseReference(): BelongsTo
    {
        return $this->belongsTo(ArticleReference::class, 'id_reference', 'id_reference');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(AccessoryMaterial::class, 'id_matiere_accessoire', 'id_matiere_accessoire');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_categorie', 'id_categorie');
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(
            Characteristic::class,
            'caracterise',
            'id_article',
            'id_caracteristique'
        )->withPivot('valeur_caracteristique');
    }

    public function similar(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'similaire', 'id_article_simil', 'id_article');
    }

    public function availableSizes(): BelongsToMany
    {
        return $this->belongsToMany(
            Size::class,
            'taille_dispo',
            'id_reference',
            'id_taille'
        )->withPivot('dispo_en_ligne');
    }

    public function shopAvailabilities(): BelongsToMany
    {
        return $this->belongsToMany(
            Shop::class,
            'dispo_magasin',
            'id_reference',
            'id_magasin'
        )->withPivot(['id_taille', 'statut']);
    }
}

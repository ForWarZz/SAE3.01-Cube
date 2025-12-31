<?php

namespace App\Models;

use Carbon\Carbon;
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
 * @property Carbon|null $date_ajout
 * @property int $nombre_vente_article
 * @property float $poids_article
 */
class Bike extends BaseArticle
{
    protected $table = 'velo';

    protected $fillable = [
        'id_article',
        'id_millesime',
        'id_modele_velo',
        'id_materiau_cadre',
        'prix_article',
        'id_categorie',
        'id_usage',
        'nom_article',
        'description_article',
        'resumer_article',
        'date_ajout',
        'nombre_vente_article',
        'poids_article',
    ];

    protected $casts = [
        'date_ajout' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('active_article', function ($builder) {
            $builder->whereHas('article');
        });
    }

    public function isNew(): bool
    {
        if (! $this->date_ajout) {
            return false;
        }

        return $this->date_ajout->greaterThan(Carbon::now()->subMonths(6));
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function references(): HasMany
    {
        return $this->hasMany(BikeReference::class, 'id_article', 'id_article');
    }

    public function ebikeReferences(): HasMany
    {
        return $this->hasMany(EBikeReference::class, 'id_article', 'id_article');
    }

    public function ebike(): BelongsTo
    {
        return $this->belongsTo(EBike::class, 'id_article', 'id_article');
    }

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

    public function usage(): BelongsTo
    {
        return $this->belongsTo(Usage::class, 'id_usage', 'id_usage');
    }

    public function compatibleAccessories(): BelongsToMany
    {
        return $this->belongsToMany(Accessory::class,
            'compatible',
            'vel_id_article',
            'id_article',
            'id_article',
            'id_article'
        );
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

    //    public const WEIGHT_CHARACTERISTIC_ID = 22;
    //
    //    public $timestamps = false;
    //
    //    protected $table = 'velo';
    //
    //    protected $primaryKey = 'id_article';
    //
    //    protected $fillable = [
    //        'id_article',
    //        'id_millesime',
    //        'id_modele_velo',
    //        'id_materiau_cadre',
    //        'prix_article',
    //        'id_categorie',
    //        'id_usage',
    //        'nom_article',
    //        'description_article',
    //        'resumer_article',
    //        'date_ajout',
    //        'nombre_vente_article',
    //    ];
    //
    //    protected $casts = [
    //        'date_ajout' => 'datetime',
    //    ];
    //
    //    protected static function booted(): void
    //    {
    //        // Permet de filtrer au cas ou le l'article ait été supprimé via un soft delete, pour ne pas apparaitre dans les listes...
    //        static::addGlobalScope('active_article', function ($builder) {
    //            $builder->whereHas('article');
    //        });
    //    }
    //
    //    /**
    //     * Check if the bike was added less than 6 months ago
    //     */
    //    public function isNew(): bool
    //    {
    //        if (! $this->date_ajout) {
    //            return false;
    //        }
    //
    //        return $this->date_ajout->greaterThan(Carbon::now()->subMonths(6));
    //    }
    //
    //    public function ebike(): BelongsTo
    //    {
    //        return $this->belongsTo(EBike::class, 'id_article', 'id_article');
    //    }
    //
    //    public function frameMaterial(): BelongsTo
    //    {
    //        return $this->belongsTo(BikeFrameMaterial::class, 'id_materiau_cadre', 'id_materiau_cadre');
    //    }
    //
    //    public function vintage(): BelongsTo
    //    {
    //        return $this->belongsTo(Vintage::class, 'id_millesime', 'id_millesime');
    //    }
    //
    //    public function bikeModel(): BelongsTo
    //    {
    //        return $this->belongsTo(BikeModel::class, 'id_modele_velo', 'id_modele_velo');
    //    }
    //
    //    public function article(): BelongsTo
    //    {
    //        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    //    }
    //
    //    public function references(): HasMany
    //    {
    //        return $this->hasMany(BikeReference::class, 'id_article', 'id_article');
    //    }
    //
    //    public function ebikeReferences(): HasMany
    //    {
    //        return $this->hasMany(EBikeReference::class, 'id_article', 'id_article');
    //    }
    //
    //    public function usage(): BelongsTo
    //    {
    //        return $this->belongsTo(Usage::class, 'id_usage', 'id_usage');
    //    }
    //
    //    public function compatibleAccessories(): BelongsToMany
    //    {
    //        return $this->belongsToMany(Accessory::class,
    //            'compatible',
    //            'vel_id_article',
    //            'id_article',
    //            'id_article',
    //            'id_article'
    //        );
    //    }
    //
    //    public function category(): BelongsTo
    //    {
    //        return $this->belongsTo(Category::class, 'id_categorie', 'id_categorie');
    //    }
    //
    //    public function characteristics(): BelongsToMany
    //    {
    //        return $this->belongsToMany(
    //            Characteristic::class,
    //            'caracterise',
    //            'id_article',
    //            'id_caracteristique'
    //        )->withPivot('valeur_caracteristique');
    //    }
    //
    //    public function similar(): BelongsToMany
    //    {
    //        return $this->belongsToMany(Article::class, 'similaire', 'id_article_simil', 'id_article');
    //    }
}

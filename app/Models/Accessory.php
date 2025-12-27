<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id_article
 * @property int $id_reference
 * @property int $id_matiere_accessoire
 * @property float $prix_article
 * @property int $id_categorie
 * @property string $nom_article
 * @property string $description_article
 * @property string $resumer_article
 * @property int $numero_reference
 * @property int $pourcentage_remise
 * @property int $nombre_vente_article
 * @property float $poids_article
 */
class Accessory extends BaseArticle
{
    public const WEIGHT_CHARACTERISTIC_ID = 31;

    protected $table = 'accessoire';

    protected $fillable = [
        'id_article',
        'id_reference',
        'id_matiere_accessoire',
        'prix_article',
        'id_categorie',
        'nom_article',
        'description_article',
        'resumer_article',
        'numero_reference',
        'pourcentage_remise',
        'nombre_vente_article',
        'poids_article',
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

    public function getCoverUrl($referenceId = null): string
    {
        $files = $this->getImageFiles();

        if (empty($files)) {
            return '';
        }

        return Storage::url($this->getImageFiles()[0]);
    }

    public function getImageFiles(bool $is360 = false): array
    {
        $directory = $this->getStorageDirectory();

        if ($is360) {
            $directory .= '/360';
        }

        return Storage::disk('public')->files($directory);
    }

    public function getStorageDirectory(): string
    {
        return "articles/$this->id_article/$this->id_reference/";
    }

    public function getImagesUrls(bool $is360 = false): array
    {
        $files = $this->getImageFiles($is360);

        return array_map(fn ($f) => Storage::url($f), $files);
    }

    public function getImagePathFromName(string $imageName): string
    {
        return $this->getStorageDirectory().$imageName;
    }
}

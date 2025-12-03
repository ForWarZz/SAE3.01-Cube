<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id_article
 * @property int $id_categorie
 * @property float $prix_article
 * @property string $nom_article
 * @property string $description_article
 * @property string $resumer_article
 * @property int $nombre_vente_article
 * @property int pourcentage_remise
 */
class Article extends Model
{
    protected $table = 'article';

    protected $primaryKey = 'id_article';

    protected $fillable = [
        'id_article',
        'id_categorie',
        'prix_article',
        'nom_article',
        'description_article',
        'resumer_article',
        'nombre_vente_article',
        'pourcentage_remise',
    ];

    public function hasDiscount(): bool
    {
        return $this->pourcentage_remise > 0;
    }

    public function getDiscountedPrice(): float
    {
        if ($this->hasDiscount() > 0) {
            return round($this->prix_article * (1 - $this->pourcentage_remise / 100), 2);
        }

        return $this->prix_article;
    }

    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class, 'id_article', 'id_article');
    }

    public function bike(): HasOne
    {
        return $this->hasOne(Bike::class, 'id_article', 'id_article');
    }

    public function similar(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'similaire', 'id_article_simil', 'id_article');
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

    public function getCoverUrl($colorId = null): string
    {
        if ($colorId) {
            return Storage::url("articles/$this->id_article/$colorId/1.jpg");
        }

        $firstRef = BikeReference::where('id_article', $this->id_article)->first();
        if ($firstRef) {
            return Storage::url("articles/$this->id_article/$firstRef->id_couleur/1.jpg");
        }

        return Storage::url("articles/$this->id_article/default/1.jpg");
    }

    public function getAllImagesUrls($colorId = null): array
    {
        $folder = $colorId ?: 'default';
        $directory = "articles/$this->id_article/$folder";

        $files = Storage::disk('public')->files($directory);

        return array_map(fn ($f) => Storage::url($f), $files);
    }

    public function isBike(): bool
    {
        return $this->bike()->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id_categorie
 * @property float $prix_article
 * @property string $nom_article
 * @property string $description_article
 * @property string $resumer_article
 */
class Article extends Model
{
    protected $table = 'article';
    protected $primaryKey = 'id_article';
    protected $fillable = ['id_categorie', 'prix_article', 'nom_article', 'description_article', 'resumer_article'];

    public function accessoires(): HasMany
    {
        return $this->hasMany('App\Models\Accessoire', 'id_article', 'id_article');
    }

    public function velo(): HasOne
    {
        return $this->hasOne(Velo::class, 'id_article', 'id_article');
    }

    public function similaires(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'similaire', 'id_article_simil', 'id_article');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }

    public function caracteristiques(): BelongsToMany
    {
        return $this->belongsToMany(
            Caracteristique::class,
            'caracterise',
            'id_article',
            'id_caracteristique'
        )->withPivot('valeur_caracteristique');
    }
}

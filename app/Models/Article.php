<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_article
 * @property integer $id_categorie
 * @property float $prix_article
 * @property string $nom_article
 * @property string $description_article
 * @property string $resumer_article
 * @property Accessoire[] $accessoires
 * @property Velo $velo
 * @property Article[] $articles
 * @property Categorie $categorie
 * @property Caracterise[] $caracterises
 */
class Article extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'article';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_article';

    /**
     * @var array
     */
    protected $fillable = ['id_categorie', 'prix_article', 'nom_article', 'description_article', 'resumer_article'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessoires()
    {
        return $this->hasMany('App\Models\Accessoire', 'id_article', 'id_article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function velo()
    {
        return $this->hasOne('App\Models\Velo', 'id_article', 'id_article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function articles()
    {
        return $this->belongsToMany('App\Models\Article', 'similaire', 'id_article_simil', 'id_article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categorie()
    {
        return $this->belongsTo('App\Models\Categorie', 'id_categorie', 'id_categorie');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function caracterises()
    {
        return $this->hasMany('App\Models\Caracterise', 'id_article', 'id_article');
    }
}

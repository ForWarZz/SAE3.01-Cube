<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_article
 * @property integer $id_reference
 * @property float $prix_article
 * @property integer $id_categorie
 * @property string $nom_article
 * @property string $description_article
 * @property string $resumer_article
 * @property Article $article
 * @property ReferenceArticle $referenceArticle
 */
class Accessoire extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'accessoire';

    /**
     * @var array
     */
    protected $fillable = ['prix_article', 'id_categorie', 'nom_article', 'description_article', 'resumer_article'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article()
    {
        return $this->belongsTo('App\Models\Article', 'id_article', 'id_article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referenceArticle()
    {
        return $this->belongsTo('App\Models\ReferenceArticle', 'id_reference', 'id_reference');
    }
}

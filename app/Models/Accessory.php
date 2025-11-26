<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
class Accessory extends Model
{
    protected $table = 'accessoire';
    protected $fillable = [
        'prix_article',
        'id_categorie',
        'nom_article',
        'description_article',
        'resumer_article'
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

//    public function referenceArticle()
//    {
//        return $this->belongsTo('App\Models\ReferenceArticle', 'id_reference', 'id_reference');
//    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    protected $table = 'accessoire';

    protected $primaryKey = 'id_article';

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
}

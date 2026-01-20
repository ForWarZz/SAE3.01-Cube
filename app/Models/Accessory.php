<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasReference;
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
 * @property int $numero_reference
 * @property int $pourcentage_remise
 * @property int $nombre_vente_article
 * @property float $poids_article
 */
class Accessory extends Article
{
    use HasImages;
    use HasReference;

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
    // availableSizes et shopAvailabilities fournis par HasReference
}

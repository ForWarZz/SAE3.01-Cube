<?php

namespace App\Models;

use App\Models\Concerns\HasArticle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @property float $poids_article
 */
class Article extends Model
{
    use HasArticle;
    use SoftDeletes;

    public $timestamps = false;

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
        'poids_article',
    ];

    public function getCoverUrl(?int $referenceId = null): string
    {
        $pathsToTry = [];

        if ($referenceId) {
            $pathsToTry[] = "articles/{$this->id_article}/{$referenceId}/thumbnail.webp";
        }

        if ($this->relationLoaded('bike') && $this->bike && $this->bike->references->isNotEmpty()) {
            $refId = $this->bike->references->first()->id_reference;
            $pathsToTry[] = "articles/{$this->id_article}/{$refId}/thumbnail.webp";
        }

        if ($this->relationLoaded('accessory') && $this->accessory) {
            $pathsToTry[] = "articles/{$this->id_article}/{$this->accessory->id_reference}/thumbnail.webp";
        }

        foreach ($pathsToTry as $path) {
            if (Storage::exists($path)) {
                return Storage::url($path);
            }
        }

        foreach ($pathsToTry as $basePath) {
            $dir = dirname($basePath);
            if (! Storage::exists($dir)) {
                continue;
            }

            $files = Storage::files($dir);

            $cover = collect($files)
                ->first(fn ($file) => preg_match('/\/1\.(jpg|jpeg|png|webp)$/i', $file));

            if ($cover) {
                return Storage::url($cover);
            }
        }

        return Storage::url("articles/{$this->id_article}/default/1.jpg");
    }
}

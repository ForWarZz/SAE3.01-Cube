<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_categorie
 * @property int $id_categorie_parent
 * @property string $nom_categorie
 */
class Category extends Model
{
    public const BIKE_CATEGORY_ID = 51;

    public const EBIKE_CATEGORY_ID = 36;

    public const ACCESSORY_CATEGORY_ID = 1;

    public $timestamps = false;

    protected $table = 'categorie';

    protected $primaryKey = 'id_categorie';

    protected $fillable = [
        'id_categorie_parent',
        'nom_categorie',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'id_categorie', 'id_categorie');
    }

    public function parentRecursive(): BelongsTo
    {
        return $this->parent()->with('parentRecursive');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_categorie_parent', 'id_categorie');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with([
            'childrenRecursive',
            'articles.bike.bikeModel',
        ]);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'id_categorie_parent', 'id_categorie');
    }

    public function getAllChildrenIds(): array
    {
        // On commence avec l'ID courant
        $ids = [$this->id_categorie];

        // On charge les enfants récursivement s'ils ne le sont pas déjà
        // (loadMissing est intelligent et ne fait rien si c'est déjà chargé)
        $this->loadMissing('childrenRecursive');

        foreach ($this->childrenRecursive as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }

        return $ids;
    }

    public function getFullPath(): string
    {
        // Plus besoin de map, on peut itérer directement sur le tableau d'objets
        $noms = [];
        $ancestors = $this->getAncestors();

        foreach ($ancestors as $cat) {
            $noms[] = $cat->nom_categorie;
        }
        $noms[] = $this->nom_categorie;

        return implode(' > ', $noms);
    }

    /**
     * ✅ OPTIMISATION 3 : getAncestors sans boucle SQL
     * Utilise la relation parentRecursive pour éviter le N+1
     *
     * @return Category[]
     */
    public function getAncestors(): array
    {
        $ancestors = [];

        // On charge toute la chaîne de parents en 1 seule requête SQL
        $this->loadMissing('parentRecursive');

        $current = $this->parentRecursive;

        while ($current) {
            // On ajoute au début du tableau pour avoir l'ordre [Grand-père, Père]
            array_unshift($ancestors, $current);
            $current = $current->parentRecursive;
        }

        return $ancestors;
    }
}

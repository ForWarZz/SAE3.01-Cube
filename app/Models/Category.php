<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    /**
     * @return int[]
     */
    public function getAllChildrenIds(?Collection $allCategories = null): array
    {
        if (is_null($allCategories)) {
            $allCategories = Category::all(['id_categorie', 'id_categorie_parent']);
        }

        $ids = [$this->id_categorie];
        $children = $allCategories->where('id_categorie_parent', $this->id_categorie);

        foreach ($children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds($allCategories));
        }

        return $ids;
    }

    public function getFullPath(): string
    {
        $parents = $this->getAncestors();
        $noms = array_map(fn ($cat) => $cat->nom_categorie, $parents);
        $noms[] = $this->nom_categorie;

        return implode(' > ', $noms);
    }

    /**
     * @return Category[]
     */
    public function getAncestors(): array
    {
        $ancestors = [];
        $currentCategory = $this;

        $currentCategory->load(['parent']);

        while ($currentCategory->parent) {
            $ancestors[] = $currentCategory->parent;
            $currentCategory = $currentCategory->parent;
        }

        return array_reverse($ancestors);
    }
}

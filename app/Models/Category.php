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
    protected $table = 'categorie';

    protected $primaryKey = 'id_categorie';

    public $timestamps = false;

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

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'id_categorie_parent', 'id_categorie');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with([
            'childrenRecursive',
            'articles.bike.bikeModel',
        ]);
    }

    /**
     * @return int[]
     */
    public function getAllChildrenIds(?Collection $allCategories = null): array
    {
        if (is_null($allCategories)) {
            $allCategories = Category::all();
        }

        $ids = [$this->id_categorie];
        $children = $allCategories->where('id_categorie_parent', $this->id_categorie);

        foreach ($children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds($allCategories));
        }

        return $ids;
    }

    /**
     * @return Category[]
     */
    public function getAncestors(): array
    {
        $ancestors = [];
        $currentCategory = $this;

        while ($currentCategory->parent) {
            $ancestors[] = $currentCategory->parent;
            $currentCategory = $currentCategory->parent;
        }

        return array_reverse($ancestors);
    }

    public function getFullPath(): string
    {
        $parents = $this->getAncestors();
        $noms = array_map(fn($cat) => $cat->nom_categorie, $parents);
        $noms[] = $this->nom_categorie;

        return implode(' > ', $noms);
    }
}

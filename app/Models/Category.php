<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property integer $id_categorie
 * @property integer $id_categorie_parent
 * @property string $nom_categorie
 */
class Category extends Model
{
    protected $table = 'categorie';
    protected $primaryKey = 'id_categorie';
    protected $fillable = [
        'id_categorie_parent',
        'nom_categorie'
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
        return $this->hasMany('App\Models\Category', 'id_categorie_parent', 'id_categorie');
    }

    /**
     * @param ?Collection $allCategories
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
}

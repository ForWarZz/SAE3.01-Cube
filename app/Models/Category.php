<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Get all children categories recursively including this one
     */
    public function getAllChildrenIds(){
        $ids = collect([$this->id_categorie]);

        foreach($this->children as $child){
            $ids = $ids->merge($child->getAllChildrenIds());
        }

        return $ids;
    }
}

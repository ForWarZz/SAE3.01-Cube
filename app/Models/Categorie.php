<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_categorie
 * @property integer $id_categorie_parent
 * @property string $nom_categorie
 * @property Article[] $articles
 * @property Categorie $categorie
 */
class Categorie extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'categorie';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_categorie';

    /**
     * @var array
     */
    protected $fillable = ['id_categorie_parent', 'nom_categorie'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany('App\Models\Article', 'id_categorie', 'id_categorie');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categorie()
    {
        return $this->belongsTo('App\Models\Categorie', 'id_categorie_parent', 'id_categorie');
    }

    public function catEnfants()
    {
        return $this->hasMany('App\Models\Categorie', 'id_categorie_parent', 'id_categorie');
    }

    public function allChildren(){
        $ids = collect([$this->id_categorie]);
        foreach($this->catEnfants as $child){
            $ids = $ids->merge($child->allChildren());
        }
        return $ids;
    }
}

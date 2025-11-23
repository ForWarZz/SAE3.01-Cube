<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_modele_velo
 * @property string $nom_modele_velo
 * @property Velo[] $velos
 * @property DeGeometrie[] $deGeometries
 */
class ModeleVelo extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'modele_velo';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_modele_velo';

    /**
     * @var array
     */
    protected $fillable = ['nom_modele_velo'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function velos()
    {
        return $this->hasMany('App\Models\Velo', 'id_modele_velo', 'id_modele_velo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deGeometries()
    {
        return $this->hasMany('App\Models\DeGeometrie', 'id_modele_velo', 'id_modele_velo');
    }
}

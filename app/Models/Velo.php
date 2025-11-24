<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Velo extends Model
{
    protected $table = 'velo';
    protected $primaryKey = 'id_article';

    protected $fillable = ['id_millesime', 'id_modele_velo', 'id_materiau_cadre', 'prix_article', 'id_categorie', 'nom_article', 'description_article', 'resumer_article'];

//    public function accessoiresCompatibles()
//    {
//        return $this->hasMany('App\Models\Compatible', 'vel_id_article', 'id_article');
//    }

    public function materiauCadreVelo(): BelongsTo
    {
        return $this->belongsTo('App\Models\MateriauCadreVelo', 'id_materiau_cadre', 'id_materiau_cadre');
    }

    public function millesime(): BelongsTo
    {
        return $this->belongsTo('App\Models\Millesime', 'id_millesime', 'id_millesime');
    }

    public function modeleVelo(): BelongsTo
    {
        return $this->belongsTo('App\Models\ModeleVelo', 'id_modele_velo', 'id_modele_velo');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo('App\Models\Article', 'id_article', 'id_article');
    }
//
//    public function veloMusculaire()
//    {
//        return $this->hasOne('App\Models\VeloMusculaire', 'id_article', 'id_article');
//    }
//
//    public function vae()
//    {
//        return $this->hasOne('App\Models\Vae', 'id_article', 'id_article');
//    }
//
//    public function referenceVelos()
//    {
//        return $this->hasMany('App\Models\ReferenceVelo', 'id_article', 'id_article');
//    }
}

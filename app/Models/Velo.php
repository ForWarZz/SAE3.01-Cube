<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_article
 * @property integer $id_millesime
 * @property integer $id_modele_velo
 * @property integer $id_materiau_cadre
 * @property float $prix_article
 * @property integer $id_categorie
 * @property string $nom_article
 * @property string $description_article
 * @property string $resumer_article
 * @property Compatible[] $compatibles
 * @property MateriauCadreVelo $materiauCadreVelo
 * @property Millesime $millesime
 * @property ModeleVelo $modeleVelo
 * @property Article $article
 * @property VeloMusculaire $veloMusculaire
 * @property Vae $vae
 * @property ReferenceVelo[] $referenceVelos
 */
class Velo extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'velo';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_article';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['id_millesime', 'id_modele_velo', 'id_materiau_cadre', 'prix_article', 'id_categorie', 'nom_article', 'description_article', 'resumer_article'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function compatibles()
    {
        return $this->hasMany('App\Models\Compatible', 'vel_id_article', 'id_article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materiauCadreVelo()
    {
        return $this->belongsTo('App\Models\MateriauCadreVelo', 'id_materiau_cadre', 'id_materiau_cadre');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function millesime()
    {
        return $this->belongsTo('App\Models\Millesime', 'id_millesime', 'id_millesime');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modeleVelo()
    {
        return $this->belongsTo('App\Models\ModeleVelo', 'id_modele_velo', 'id_modele_velo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article()
    {
        return $this->belongsTo('App\Models\Article', 'id_article', 'id_article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function veloMusculaire()
    {
        return $this->hasOne('App\Models\VeloMusculaire', 'id_article', 'id_article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vae()
    {
        return $this->hasOne('App\Models\Vae', 'id_article', 'id_article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referenceVelos()
    {
        return $this->hasMany('App\Models\ReferenceVelo', 'id_article', 'id_article');
    }
}

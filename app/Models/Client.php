<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_client
 * @property string $nom_client
 * @property string $prenom_client
 * @property string $email_client
 * @property string $naissance_client
 * @property string $civilite
 * @property string $hash_mdp_client
 * @property string $date_der_connexion
 * @property Adresse[] $adresses
 * @property DemandeServiceClient[] $demandeServiceClients
 * @property Commande[] $commandes
 * @property VeloEnregistre[] $veloEnregistres
 */
class Client extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'client';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_client';

    /**
     * @var array
     */
    protected $fillable = ['nom_client', 'prenom_client', 'email_client', 'naissance_client', 'civilite', 'hash_mdp_client', 'date_der_connexion'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adresses()
    {
        return $this->hasMany('App\Models\Adresse', 'id_client', 'id_client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function demandeServiceClients()
    {
        return $this->hasMany('App\Models\DemandeServiceClient', 'id_client', 'id_client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commandes()
    {
        return $this->hasMany('App\Models\Commande', 'id_client', 'id_client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function veloEnregistres()
    {
        return $this->hasMany('App\Models\VeloEnregistre', 'id_client', 'id_client');
    }
}

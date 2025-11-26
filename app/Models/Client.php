<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_client
 * @property string $nom_client
 * @property string $prenom_client
 * @property string $email_client
 * @property string $naissance_client
 * @property string $civilite
 * @property string $hash_mdp_client
 * @property string $date_der_connexion
 */
class Client extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id_client';
    protected $fillable = ['nom_client', 'prenom_client', 'email_client', 'naissance_client', 'civilite', 'hash_mdp_client', 'date_der_connexion'];

    public function addresses(): HasMany
    {
        return $this->hasMany('App\Models\Adresse', 'id_client', 'id_client');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany('App\Models\DemandeServiceClient', 'id_client', 'id_client');
    }

    public function orders(): HasMany
    {
        return $this->hasMany('App\Models\Commande', 'id_client', 'id_client');
    }

    public function registeredBikes(): HasMany
    {
        return $this->hasMany('App\Models\VeloEnregistre', 'id_client', 'id_client');
    }
}

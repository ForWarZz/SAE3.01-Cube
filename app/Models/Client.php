<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id_client';
    protected $fillable = ['nom_client', 'prenom_client', 'email_client', 'naissance_client', 'civilite', 'hash_mdp_client', 'date_der_connexion'];

    public function adresses(): HasMany
    {
        return $this->hasMany('App\Models\Adresse', 'id_client', 'id_client');
    }

    public function demandeServiceClients(): HasMany
    {
        return $this->hasMany('App\Models\DemandeServiceClient', 'id_client', 'id_client');
    }

    public function commandes(): HasMany
    {
        return $this->hasMany('App\Models\Commande', 'id_client', 'id_client');
    }

    public function veloEnregistres(): HasMany
    {
        return $this->hasMany('App\Models\VeloEnregistre', 'id_client', 'id_client');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id_client
 * @property string $nom_client
 * @property string $prenom_client
 * @property string $email_client
 * @property string $naissance_client
 * @property string $civilite
 * @property string $hash_mdp_client
 * @property string $date_der_connexion
 * @property string|null $google_id
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \DateTime|null $two_factor_confirmed_at
 */
class Client extends Authenticatable
{
    use Notifiable;

    protected $table = 'client';

    protected $primaryKey = 'id_client';

    public $timestamps = false;

    protected $fillable = ['nom_client', 'prenom_client', 'email_client', 'naissance_client', 'civilite', 'hash_mdp_client', 'date_der_connexion', 'google_id', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'hash_mdp_client',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'two_factor_confirmed_at' => 'datetime',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        return $this->hash_mdp_client;
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id_client';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->id_client;
    }

    /**
     * Get the email address for the user.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email_client;
    }

    public function addresses(): HasMany
    {
        return $this->hasMany('App\Models\Adresse', 'id_client', 'id_client');
    }

    // TODO: Uncomment when model is created
    // public function serviceRequests(): HasMany
    // {
    //     return $this->hasMany('App\Models\DemandeServiceClient', 'id_client', 'id_client');
    // }

    // TODO: Uncomment when models are created
    // public function orders(): HasMany
    // {
    //     return $this->hasMany('App\Models\Commande', 'id_client', 'id_client');
    // }

    // public function registeredBikes(): HasMany
    // {
    //     return $this->hasMany('App\Models\VeloEnregistre', 'id_client', 'id_client');
    // }
}

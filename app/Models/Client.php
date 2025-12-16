<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

/**
 * @property int $id_client
 * @property string $nom_client
 * @property string $prenom_client
 * @property string $email_client
 * @property Carbon $naissance_client
 * @property string $civilite
 * @property string $hash_mdp_client
 * @property Carbon $date_der_connexion
 * @property string|null $stripe_id
 */
class Client extends Authenticatable
{
    use Billable, Notifiable, SoftDeletes;

    protected $table = 'client';

    protected $primaryKey = 'id_client';

    public $timestamps = false;

    protected $fillable = [
        'nom_client',
        'prenom_client',
        'email_client',
        'naissance_client',
        'civilite',
        'hash_mdp_client',
        'date_der_connexion',
        'stripe_id',
        'google_id',
    ];

    protected $casts = [
        'naissance_client' => 'date',
        'date_der_connexion' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'hash_mdp_client',
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

    public function getLoginIdentifierName(): string
    {
        return 'email_client';
    }

    /**
     * Get the email address for the user.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email_client;
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): string
    {
        return $this->email_client;
    }

    public function stripeEmail(): ?string
    {
        return $this->email_client;
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'id_client', 'id_client');
    }

    // TODO: Uncomment when model is created
    // public function serviceRequests(): HasMany
    // {
    //     return $this->hasMany('App\Models\DemandeServiceClient', 'id_client', 'id_client');
    // }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_client', 'id_client');
    }
    //
    //    public function registeredBikes(): HasMany
    //    {
    //        return $this->hasMany('App\Models\VeloEnregistre', 'id_client', 'id_client');
    //    }
}

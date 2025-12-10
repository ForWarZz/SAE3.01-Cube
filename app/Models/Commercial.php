<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id_commercial
 * @property string $nom_commercial
 * @property string $prenom_commercial
 * @property string $email_commercial
 * @property string $hash_mdp_commercial
 */
class Commercial extends Authenticatable
{
    use Notifiable;

    protected $table = 'commercial';

    protected $primaryKey = 'id_commercial';

    public $timestamps = false;

    protected $fillable = [
        'nom_commercial',
        'prenom_commercial',
        'email_commercial',
        'hash_mdp_commercial',
    ];

    protected $hidden = [
        'hash_mdp_commercial',
    ];

    public function getAuthPassword(): string
    {
        return $this->hash_mdp_commercial;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id_commercial';
    }

    public function getAuthIdentifier()
    {
        return $this->id_commercial;
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->email_commercial;
    }
}

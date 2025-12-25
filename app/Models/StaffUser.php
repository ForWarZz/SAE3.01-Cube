<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id_staff
 * @property string $nom_staff
 * @property string $prenom_staff
 * @property string $email_staff
 * @property string $hash_mdp_staff
 * @property string $role
 */
class StaffUser extends Authenticatable
{
    use Notifiable;

    public const COMMERCIAL_ROLE = 'COMMERCIAL';

    public const COMMERCIAL_DIRECTOR_ROLE = 'DIRECTOR';

    public const DPO_ROLE = 'DPO';

    public $timestamps = false;

    protected $table = 'staff';

    protected $primaryKey = 'id_staff';

    protected $fillable = [
        'nom_staff',
        'prenom_staff',
        'email_staff',
        'hash_mdp_staff',
        'role',
    ];

    protected $hidden = [
        'hash_mdp_staff',
    ];

    public function getAuthPassword(): string
    {
        return $this->hash_mdp_staff;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id_staff';
    }

    public function getAuthIdentifier()
    {
        return $this->id_staff;
    }

    public function getLoginIdentifierName(): string
    {
        return 'email_staff';
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->email_staff;
    }

    public function isCommercial(): bool
    {
        return $this->role === self::COMMERCIAL_ROLE || $this->role === self::COMMERCIAL_DIRECTOR_ROLE;
    }

    public function isCommercialDirector(): bool
    {
        return $this->role === self::COMMERCIAL_DIRECTOR_ROLE;
    }

    public function isDPO(): bool
    {
        return $this->role === self::DPO_ROLE;
    }
}

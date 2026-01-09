<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_etat_retour
 * @property string $label_etat_retour
 */
class OrderReturnState extends Model
{
    protected $primaryKey = 'id_etat_retour';

    public $timestamps = false;

    protected $table = 'etat_retour';

    protected $fillable = [
        'id_etat_retour',
        'label_etat_retour',
    ];
}

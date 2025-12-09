<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_type_paiement
 * @property string $label_type_paiement
 * @property string $stripe_type
 */
class PaymentType extends Model
{
    public const UNKNOWN = 3;

    public $timestamps = false;

    protected $primaryKey = 'id_type_paiement';

    protected $table = 'type_paiement';

    protected $fillable = [
        'id_type_paiement',
        'label_type_paiement',
        'stripe_type',
    ];
}

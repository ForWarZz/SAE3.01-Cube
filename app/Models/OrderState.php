<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_etat
 * @property string $label_etat
 */
class OrderState extends Model
{
    public const PENDING_PAYMENT = 1;

    public const PAYMENT_ACCEPTED = 2;

    public const SHIPPED = 3;

    public const DELIVERED = 4;

    public const CANCELLED = 5;

    public const RETURNED = 6;

    public $timestamps = false;

    protected $primaryKey = 'id_etat';

    protected $table = 'etat_commande';

    protected $fillable = [
        'id_etat',
        'label_etat',
    ];
}

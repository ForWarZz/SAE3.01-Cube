<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_moyen_livraison
 * @property string $label_moyen_livraison
 */
class ShippingMode extends Model
{
    public const CLICK_AND_COLLECT = 1;

    public const HOME_DELIVERY = 2;

    public $timestamps = false;

    protected $primaryKey = 'id_moyen_livraison';

    protected $table = 'moyen_livraison';

    protected $fillable = [
        'id_moyen_livraison',
        'label_moyen_livraison',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_code_promo
 * @property float $pourcentage_remise
 * @property string $label_code_promo
 * @property bool $est_actif
 */
class DiscountCode extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_code_promo';

    protected $table = 'code_promo';

    protected $fillable = [
        'id_code_promo',
        'pourcentage_remise',
        'label_code_promo',
        'est_actif',
    ];
}

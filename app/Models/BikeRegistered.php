<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_velo_enr
 * @property int $id_client
 * @property int $id_magasin
 * @property string|null $num_serie_velo_enr
 * @property Carbon|null $date_achat_velo_enr
 * @property string $chemin_facture_velo_enr
 * @property string|null $millesime_velo_enr
 */
class BikeRegistered extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_velo_enr';

    protected $table = 'velo_enregistre';

    protected $fillable = [
        'id_velo_enr',
        'id_client',
        'id_magasin',
        'num_serie_velo_enr',
        'date_achat_velo_enr',
        'chemin_facture_velo_enr',
        'millesime_velo_enr',
    ];

    protected $casts = [
        'date_achat_velo_enr' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'id_magasin', 'id_magasin');
    }
}

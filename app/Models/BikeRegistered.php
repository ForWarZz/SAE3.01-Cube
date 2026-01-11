<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikeRegistered extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_vel_enr';

    protected $table = 'velo_enregistre';

    protected $fillable = [
        'id_vel_enr',
        'id_client',
        'id_magasin',
        'num_serie_velo_enr',
        'date_achat_velo_enr',
        'chemin_facture_velo_enr',
        'millesime_velo_enr',
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

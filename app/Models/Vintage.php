<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_millesime
 * @property string $millesime_velo
 */
class Vintage extends Model
{
    public $timestamps = false;

    protected $table = 'millesime';

    protected $primaryKey = 'id_millesime';

    protected $fillable = [
        'id_millesime',
        'millesime_velo',
    ];
}

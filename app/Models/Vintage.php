<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_millesime
 * @property string $label_millesime
 */
class Vintage extends Model
{
    public $timestamps = false;
    protected $table = 'millesime';
    protected $primaryKey = 'id_millesime';
    protected $fillable = [
        'id_millesime',
        'label_millesime'
    ];
}

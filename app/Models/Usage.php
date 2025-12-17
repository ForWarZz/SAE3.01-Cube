<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_usage
 * @property string $label_usage
 */
class Usage extends Model
{
    public $timestamps = false;

    protected $table = 'usage_velo';

    protected $primaryKey = 'id_usage';

    protected $fillable = [
        'id_usage',
        'label_usage',
    ];
}

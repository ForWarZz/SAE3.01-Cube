<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id_reference
 */
class Reference extends Model
{
    public $timestamps = false;
    protected $table = 'reference_article';
    protected $primaryKey = 'id_reference';

    protected $fillable = [
        'id_reference',
    ];

    public function bikeReference(): HasOne
    {
        return $this->hasOne(BikeReference::class, 'id_reference', 'id_reference');
    }
}

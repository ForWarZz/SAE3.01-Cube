<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batterie extends Model
{
    public $timestamps = false;

    protected $table = 'batterie';
    protected $primaryKey = 'id_batterie';
    protected $fillable = [
        'id_batterie',
        'capacite_batterie',
    ];

    public function referencesVae(): HasMany
    {
        return $this->hasMany(ReferenceVae::class, 'id_reference', 'id_reference');
    }
}

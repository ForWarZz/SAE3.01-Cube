<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EBike extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_article';

    protected $table = 'vae';
}

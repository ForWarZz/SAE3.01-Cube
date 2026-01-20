<?php

namespace App\Models;

class EBike extends Article
{
    public $timestamps = false;

    protected $primaryKey = 'id_article';

    protected $table = 'vae';
}

<?php

namespace App\Models;

class EBike extends BaseArticle
{
    public $timestamps = false;

    protected $primaryKey = 'id_article';

    protected $table = 'vae';
}

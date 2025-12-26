<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends BaseArticle
{
    use SoftDeletes;
}

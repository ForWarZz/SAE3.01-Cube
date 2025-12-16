<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id_taille
 * @property string $nom_taille
 * @property string $pivot
 */
class Size extends Model
{
    public $timestamps = false;

    protected $table = 'taille';

    protected $primaryKey = 'id_taille';

    protected $fillable = [
        'id_taille',
        'nom_taille',
    ];

    public function references(): BelongsToMany
    {
        return $this->belongsToMany(ArticleReference::class, 'taille_dispo', 'id_taille', 'id_reference');
    }
}

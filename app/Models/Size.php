<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id_taille
 * @property string $nom_taille
 * @property string $pivot
 * @property int $min_val
 * @property int $max_val
 * @property string $type_article
 */
class Size extends Model
{
    public $timestamps = false;

    protected $table = 'taille';

    protected $primaryKey = 'id_taille';

    protected $fillable = [
        'id_taille',
        'nom_taille',
        'min_val',
        'max_val',
        'type_article',
    ];

    public function references(): BelongsToMany
    {
        return $this->belongsToMany(ArticleReference::class, 'taille_dispo', 'id_taille', 'id_reference');
    }

    public function label(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (is_null($attributes['min_val'])) {
                    return $attributes['nom_taille'];
                }

                if ($attributes['min_val'] == $attributes['max_val']) {
                    return "{$attributes['label']} ({$attributes['min_val']} cm)";
                }

                return "{$attributes['nom_taille']} ({$attributes['min_val']} - {$attributes['max_val']} cm)";
            }
        );
    }
}

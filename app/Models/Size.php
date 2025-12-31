<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id_taille
 * @property string $nom_taille
 * @property int $taille_min
 * @property int $taille_max
 * @property string $type_article
 */
class Size extends Model
{
    public const TYPE_ACCESSORY = 'ACCESSOIRE';

    public const TYPE_BIKE = 'VELO';

    public $timestamps = false;

    protected $table = 'taille';

    protected $primaryKey = 'id_taille';

    protected $fillable = [
        'id_taille',
        'nom_taille',
        'taille_min',
        'taille_max',
        'type_article',
    ];

    public function scopeAccessory(Builder $query): Builder
    {
        return $query->where('type_article', self::TYPE_ACCESSORY);
    }

    public function scopeBike(Builder $query): Builder
    {
        return $query->where('type_article', self::TYPE_BIKE);
    }

    public function references(): BelongsToMany
    {
        return $this->belongsToMany(ArticleReference::class, 'taille_dispo', 'id_taille', 'id_reference');
    }

    public function label(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (is_null($attributes['taille_min'])) {
                    return $attributes['nom_taille'];
                }

                if ($attributes['taille_min'] == $attributes['taille_max']) {
                    return "{$attributes['label']} ({$attributes['taille_min']} cm)";
                }

                return "{$attributes['nom_taille']} ({$attributes['taille_min']} - {$attributes['taille_max']} cm)";
            }
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopAvailability extends Model
{
    const STATUS_IN_STOCK = 'En Stock';

    const STATUS_ORDERABLE = 'Commandable';

    const STATUS_UNAVAILABLE = 'Indisponible';

    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'dispo_magasin';

    protected $fillable = [
        'id_reference',
        'id_taille',
        'id_magasin',
        'statut',
    ];

    public function reference(): BelongsTo
    {
        return $this->belongsTo(ArticleReference::class, 'id_reference', 'id_reference');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'id_taille', 'id_taille');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'id_magasin', 'id_magasin');
    }

    public function isInStock(): bool
    {
        return $this->statut === self::STATUS_IN_STOCK;
    }

    public function isOrderable(): bool
    {
        return $this->statut === self::STATUS_ORDERABLE;
    }

    public function isUnavailable(): bool
    {
        return $this->statut === self::STATUS_UNAVAILABLE;
    }
}

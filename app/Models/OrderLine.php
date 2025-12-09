<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLine extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_ligne';

    protected $table = 'ligne_commande';

    protected $fillable = [
        'id_ligne',
        'id_reference',
        'id_commande',
        'id_taille',
        'quantite_ligne',
        'prix_unit_ligne',
    ];

    public function reference(): BelongsTo
    {
        return $this->belongsTo(ArticleReference::class, 'id_reference', 'id_reference');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_commande', 'id_commande');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'id_taille', 'id_taille');
    }
}

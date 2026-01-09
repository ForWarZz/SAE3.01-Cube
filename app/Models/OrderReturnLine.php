<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_ligne_retour
 * @property int $id_demande_retour
 * @property int $id_ligne_commande
 * @property int $quantite_retournee
 */
class OrderReturnLine extends Model
{
    protected $primaryKey = 'id_ligne_retour';

    public $timestamps = false;

    protected $table = 'ligne_retour';

    protected $fillable = [
        'id_ligne_retour',
        'id_demande_retour',
        'id_ligne_commande',
        'quantite_retournee',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(OrderReturnRequest::class, 'id_demande_retour', 'id_demande_retour');
    }

    public function orderLine(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class, 'id_ligne_commande', 'id_ligne');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_demande_retour
 * @property int $id_commande
 * @property int $id_etat_retour
 * @property Carbon $date_demande
 * @property string $description_demande
 */
class OrderReturnRequest extends Model
{
    protected $primaryKey = 'id_demande_retour';

    public $timestamps = false;

    protected $table = 'demande_retour';

    protected $fillable = [
        'id_demande_retour',
        'id_commande',
        'id_etat_retour',
        'date_demande',
        'description_demande',
    ];

    protected $casts = [
        'date_demande' => 'datetime',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(OrderReturnLine::class, 'id_demande_retour', 'id_demande_retour');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_commande', 'id_commande');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(OrderReturnState::class, 'id_etat_retour', 'id_etat_retour');
    }
}

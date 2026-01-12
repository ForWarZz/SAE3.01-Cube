<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_piece_jointe
 * @property int $id_demande_retour
 * @property string $chemin_fichier
 */
class OrderReturnAttachment extends Model
{
    protected $table = 'piece_jointe_retour';

    protected $primaryKey = 'id_piece_jointe';

    public $timestamps = false;

    protected $fillable = [
        'id_demande_retour',
        'chemin_fichier',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(OrderReturnRequest::class, 'id_demande_retour', 'id_demande_retour');
    }

    public function getFileName(): string
    {
        return basename($this->chemin_fichier);
    }
}

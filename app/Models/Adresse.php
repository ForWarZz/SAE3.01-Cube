<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_adresse
 * @property int $id_client
 * @property int $id_ville
 * @property string $alias_adresse
 * @property string $nom_adresse
 * @property string $prenom_adresse
 * @property string $num_voie_adresse
 * @property string $rue_adresse
 * @property string $complement_adresse
 * @property string $telephone_adresse
 * @property string $tel_mobile_adresse
 * @property string $societe_adresse
 * @property string $tva_adresse
 */
class Adresse extends Model
{
    public $timestamps = false;
    protected $table = 'adresse';
    protected $primaryKey = 'id_adresse';
    protected $fillable = [
        'id_client',
        'id_ville',
        'alias_adresse',
        'nom_adresse',
        'prenom_adresse',
        'num_voie_adresse',
        'rue_adresse',
        'complement_adresse',
        'telephone_adresse',
        'tel_mobile_adresse',
        'societe_adresse',
        'tva_adresse',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'id_ville', 'id_ville');
    }
}

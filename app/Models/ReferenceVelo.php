<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReferenceVelo extends Model
{
    public $timestamps = false;

    protected $table = 'reference_velo';
    protected $primaryKey = 'id_reference';

    protected $fillable = [
        'id_reference',
        'id_cadre_velo',
        'id_couleur',
        'id_article'
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function velo(): BelongsTo
    {
        return $this->belongsTo(Velo::class, 'id_article', 'id_article');
    }

    public function referenceVae(): BelongsTo
    {
        return $this->belongsTo(ReferenceVae::class, 'id_reference', 'id_reference');
    }

    public function couleur(): BelongsTo
    {
        return $this->belongsTo(Couleur::class, 'id_couleur', 'id_couleur');
    }

    public function cadre(): BelongsTo
    {
        return $this->belongsTo(CadreVelo::class, 'id_cadre_velo', 'id_cadre_velo');
    }

    public function taillesDispo(): BelongsToMany
    {
        return $this->belongsToMany(
            TailleVelo::class,
            'taille_dispo',
            'id_reference',
            'id_taille'
        )->withPivot('dispo_en_ligne');
    }
}

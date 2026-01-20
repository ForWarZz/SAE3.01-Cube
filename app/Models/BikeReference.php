<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BikeReference extends Model
{
    use HasImages;
    use HasReference;
    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'reference_velo';

    protected $primaryKey = 'id_reference';

    protected $fillable = [
        'id_reference',
        'id_cadre_velo',
        'id_couleur',
        'id_article',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class, 'id_article', 'id_article');
    }

    public function ebike(): BelongsTo
    {
        return $this->belongsTo(EBikeReference::class, 'id_reference', 'id_reference');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'id_couleur', 'id_couleur');
    }

    public function frame(): BelongsTo
    {
        return $this->belongsTo(BikeFrame::class, 'id_cadre_velo', 'id_cadre_velo');
    }

    public function baseReference(): BelongsTo
    {
        return $this->belongsTo(ArticleReference::class, 'id_reference', 'id_reference');
    }
    // baseReference, availableSizes et shopAvailabilities fournis par HasReference
}

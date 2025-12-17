<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @class Order
 *
 * @property int $id_commande
 * @property int $id_adresse_facturation
 * @property int $id_type_paiement
 * @property int $id_adresse_livraison
 * @property int $id_magasin
 * @property int $id_moyen_livraison
 * @property int $id_client
 * @property string $num_commande
 * @property Carbon $date_commande
 * @property string|null $num_suivi_commande
 * @property float $frais_livraison
 * @property int|null $id_code_promo
 * @property float|null $pourcentage_remise
 * @property string|null $stripe_session_id
 * @property string|null $cb_last4
 * @property Carbon|null $date_paiement
 */
class Order extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_commande';

    protected $table = 'commande';

    protected $fillable = [
        'id_commande',
        'id_adresse_facturation',
        'id_type_paiement',
        'id_adresse_livraison',
        'id_magasin',
        'id_moyen_livraison',
        'id_client',
        'num_commande',
        'date_commande',
        'num_suivi_commande',
        'frais_livraison',
        'id_code_promo',
        'pourcentage_remise',
        'stripe_session_id',
        'cb_last4',
        'date_paiement',
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'date_paiement' => 'datetime',
    ];

    public function states(): BelongsToMany
    {
        return $this->belongsToMany(
            OrderState::class,
            'evolue',
            'id_commande',
            'id_etat'
        )->withPivot('date_changement')
            ->orderByPivot('date_changement', 'desc');
    }

    public function currentState(): OrderState
    {
        return $this->states->firstOrFail();
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'id_adresse_facturation', 'id_adresse')
            ->withTrashed();
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'id_adresse_livraison', 'id_adresse')
            ->withTrashed();
    }

    public function shippingMode(): BelongsTo
    {
        return $this->belongsTo(ShippingMode::class, 'id_moyen_livraison', 'id_moyen_livraison');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client')
            ->withTrashed();
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class, 'id_code_promo', 'id_code_promo');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'id_commande', 'id_commande');
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class, 'id_type_paiement', 'id_type_paiement');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'id_magasin', 'id_magasin');
    }
}

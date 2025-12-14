<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\OrderState;
use App\Models\PaymentType;
use App\Services\Cart\CartSessionManager;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\Exception\ApiErrorException;

class StripeListener
{
    public function __construct(
        protected readonly CartSessionManager $session
    ) {}

    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'checkout.session.completed') {
            $sessionRaw = $event->payload['data']['object'];
            $orderId = $sessionRaw['metadata']['order_id'] ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                $currentOrderState = $order->currentState();

                try {
                    $session = Cashier::stripe()->checkout->sessions->retrieve($sessionRaw['id'], [
                        'expand' => ['payment_intent.payment_method'],
                    ]);

                    $stripeType = $session->payment_intent->payment_method->type ?? 'unknown';
                    $paymentTypeId = PaymentType::query()
                        ->select('id_type_paiement')
                        ->where('stripe_type', $stripeType)
                        ->first()?->id_type_paiement ?? PaymentType::UNKNOWN;

                    $last4 = $session->payment_intent->payment_method->card->last4 ?? null;
                } catch (ApiErrorException $e) {
                    Log::error($e->getMessage());
                }

                if ($currentOrderState != OrderState::PAYMENT_ACCEPTED) {
                    $order->update([
                        'stripe_session_id' => $sessionRaw['id'],
                        'date_paiement' => now(),
                        'id_type_paiement' => $paymentTypeId ?? PaymentType::UNKNOWN,
                        'cb_last4' => $last4 ?? null,
                    ]);

                    $order->states()->attach(OrderState::PAYMENT_ACCEPTED, ['date_changement' => now()]);
                }
            }
        }
    }
}

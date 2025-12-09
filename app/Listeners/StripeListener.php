<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\OrderState;
use App\Services\Cart\CartSessionManager;
use Laravel\Cashier\Events\WebhookReceived;

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
            $session = $event->payload['data']['object'];
            $orderId = $session['metadata']['order_id'] ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                $currentOrderState = $order->currentState();

                if ($currentOrderState !== OrderState::PAYMENT_ACCEPTED) {
                    $order->update([
                        'stripe_session_id' => $session['id'],
                        'date_paiement' => now(),
                        'id_type_paiement' => 1,
                    ]);

                    $order->states()->attach(OrderState::PAYMENT_ACCEPTED, ['date_changement' => now()]);
                }

                //                $this->session->clearAll();
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Cart\CartSessionManager;
use App\Services\Cart\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected readonly CheckoutService $checkoutService,
        protected readonly CartSessionManager $cartSession,
    ) {}

    public function checkout()
    {
        try {
            $client = auth()->user();
            $order = $this->checkoutService->createOrder($client);
            $checkoutData = $this->checkoutService->initStripeCheckoutSession($order);

            return redirect()->to($checkoutData->url);

        } catch (\DomainException $e) {
            return redirect()
                ->route('cart.index')
                ->with('error', $e->getMessage());

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('cart.index')
                ->with('error', 'Une erreur est survenue lors de la création de la session de paiement. Veuillez réessayer plus tard.');
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('cart.index')
                ->with('error', 'Session de paiement invalide.');
        }

        $order = Order::where('stripe_session_id', $sessionId)
            ->select('id_commande', 'id_client')
            ->first();

        if (! $order) {
            return redirect()->route('cart.index')
                ->with('error', 'La commande associée à cette session de paiement est introuvable. Le processus de paiement a peut-être échoué.');
        }

        if ($order->id_client !== auth()->id()) {
            return redirect()->route('cart.index')
                ->with('error', 'Accès non autorisé.');
        }

        $this->cartSession->clearAll();

        return redirect()->route('cart.index')
            ->with([
                'success' => 'Paiement réussi ! Votre commande a été prise en compte.',
                'order_id' => $order->id_commande,
            ]);
    }

    public function cancel(Request $request)
    {
        return redirect()->route('cart.index')
            ->with('error', 'Le paiement a été annulé. Votre panier a été conservé, vous pouvez réessayer quand vous le souhaitez.');
    }
}

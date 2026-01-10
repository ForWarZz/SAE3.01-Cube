<?php

namespace App\Services\AI;

use App\Models\Category;
use App\Models\Order;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\Log;

class GeminiFunctionExecutor
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly GeminiArticleHelper $articleHelper
    ) {}

    public function execute(string $functionName, array $arguments): array
    {
        Log::info("Executing function: {$functionName}", ['arguments' => $arguments]);

        return match ($functionName) {
            'get_user_profile' => $this->getUserProfile($arguments),
            'get_user_orders' => $this->getUserOrders($arguments),
            'get_user_order_detail' => $this->getUserOrderDetails($arguments),
            'get_cart_content' => $this->getCartContent($arguments),
            'search_articles' => $this->searchArticles($arguments),
            'get_reference_details' => $this->getReferenceDetails($arguments),
            'get_reference_availability' => $this->getReferenceAvailability($arguments),
            'get_article_variants' => $this->getArticleVariants($arguments),
            'get_similar_articles' => $this->getSimilarArticles($arguments),
            'get_compatible_accessories' => $this->getCompatibleAccessories($arguments),
            'get_categories' => $this->getCategories($arguments),
            'get_current_category' => $this->getCategoryDetails($arguments),

            default => ['error' => "Fonction inconnue: {$functionName}"],
        };
    }

    private function getUserProfile(array $arguments): array
    {
        if (! auth()->check()) {
            return $this->authenticatedUserError();
        }

        $includeAddresses = $arguments['include_addresses'] ?? false;

        $user = auth()->user();

        if ($includeAddresses) {
            $user->load('addresses.city');
        }

        $response = [
            'success' => true,
            'profile' => [
                'first_name' => $user->prenom_client,
                'last_name' => $user->nom_client,
                'email' => $user->email_client,
                'has_2fa_enabled' => (bool) $user->two_factor_confirmed_at,
                'is_google_authenticated' => (bool) $user->google_id,
                'last_logged' => $user->date_der_connexion?->format('Y-m-d H:i:s'),
            ],
        ];

        if ($includeAddresses) {
            $response['addresses'] = $user->addresses->map(fn ($address) => [
                'id' => $address->id_adresse,
                'alias' => $address->alias_adresse,
                'name' => $address->nom_adresse,
                'street' => $address->rue_adresse,
                'city' => $address->city?->nom_ville,
                'postal_code' => $address->city?->postal_code,
                'phone' => $address->telephone_adresse,
                'mobile' => $address->tel_mobile_adresse,
                'company' => $address->societe_adresse,
                'vat_number' => $address->tva_adresse,
            ])->toArray();
        }

        return $response;
    }

    private function getUserOrders(array $arguments): array
    {
        if (! auth()->check()) {
            return $this->authenticatedUserError();
        }

        $user = auth()->user();
        $limit = min($arguments['limit'] ?? 10, 50);
        $includeDetails = $arguments['include_details'] ?? false;

        $query = Order::where('id_client', $user->id_client)
            ->with(['states', 'shop', 'shippingMode']);

        if ($includeDetails) {
            $query->with([
                'items.reference.article',
                'billingAddress',
                'deliveryAddress',
                'paymentType',
                'discountCode',
                'shop',
                'shippingMode',
                'states',
            ]);
        }

        $orders = $query->orderBy('date_commande', 'desc')
            ->limit($limit)
            ->get();

        return [
            'success' => true,
            'count' => $orders->count(),
            'orders' => $orders
                ->map(fn ($order) => $this->buildOrderDetails($order, $includeDetails))
                ->toArray(),
        ];
    }

    private function getUserOrderDetails(array $arguments): array
    {
        if (! auth()->check()) {
            return $this->authenticatedUserError();
        }

        $orderId = $arguments['order_id'] ?? null;
        $orderNumber = $arguments['order_number'] ?? null;

        if (! $orderId && ! $orderNumber) {
            return ['error' => 'ID de commande ou numéro de commande manquant, veuillez refaire votre demande en incluant l\'un des deux.'];
        }

        $user = auth()->user();
        $order = Order::with([
            'items.reference.article',
            'billingAddress.city',
            'deliveryAddress.city',
            'paymentType',
            'discountCode',
            'shop',
            'shippingMode',
            'states',
        ])
            ->where('id_client', $user->id_client)
            ->where(function ($query) use ($orderId, $orderNumber) {
                if ($orderId) {
                    $query->where('id_commande', $orderId);
                } elseif ($orderNumber) {
                    $query->where('num_commande', $orderNumber);
                }
            })
            ->first();

        if (! $order) {
            return ['error' => 'Commande introuvable ou non autorisée.'];
        }

        return [
            'success' => true,
            'order_details' => $this->buildOrderDetails($order),
        ];
    }

    private function getCartContent(array $arguments): array
    {
        $cartData = $this->cartService->getCartData();

        return [
            'success' => true,
            'cart' => $cartData->toArray(),
        ];
    }

    private function searchArticles(array $arguments): array
    {
        return $this->articleHelper->searchArticles($arguments);
    }

    private function getReferenceDetails(array $arguments): array
    {
        $referenceId = $arguments['reference_id'] ?? null;
        if (! $referenceId) {
            return ['error' => 'ID de la référence manquant, veuillez refaire votre demande en incluant l\'ID de la référence.'];
        }

        try {
            return $this->articleHelper->getReferenceDetails($referenceId);
        } catch (\Exception $e) {
            return ['error' => 'Référence introuvable avec l\'ID fourni.'];
        }
    }

    private function getReferenceAvailability(array $arguments): array
    {
        $referenceId = $arguments['reference_id'] ?? null;
        if (! $referenceId) {
            return ['error' => 'ID de la référence manquant, veuillez refaire votre demande en incluant l\'ID de la référence.'];
        }

        try {
            return $this->articleHelper->getReferenceAvailability($referenceId);
        } catch (\Exception $e) {
            return ['error' => 'Référence introuvable avec l\'ID fourni.'];
        }
    }

    private function getArticleVariants(array $arguments): array
    {
        $articleId = $arguments['article_id'] ?? null;
        if (! $articleId) {
            return ['error' => 'ID de l\'article manquant, veuillez refaire votre demande en incluant l\'ID de l\'article.'];
        }

        try {
            return $this->articleHelper->getArticleVariants($articleId);
        } catch (\Exception $e) {
            return ['error' => 'Article introuvable avec l\'ID fourni.'];
        }
    }

    private function getSimilarArticles(array $arguments): array
    {
        $articleId = $arguments['article_id'] ?? null;
        if (! $articleId) {
            return ['error' => 'ID de l\'article manquant, veuillez refaire votre demande en incluant l\'ID de l\'article.'];
        }

        try {
            return $this->articleHelper->getSimilarArticles($articleId);
        } catch (\Exception $e) {
            return ['error' => 'Article introuvable avec l\'ID fourni.'];
        }
    }

    private function getCompatibleAccessories(array $arguments): array
    {
        $articleId = $arguments['article_id'] ?? null;
        if (! $articleId) {
            return ['error' => 'ID de l\'article manquant, veuillez refaire votre demande en incluant l\'ID de l\'article.'];
        }

        try {
            return $this->articleHelper->getCompatibleAccessories($articleId);
        } catch (\Exception $e) {
            return ['error' => 'Article introuvable avec l\'ID fourni.'];
        }
    }

    private function getCategories(array $arguments): array
    {
        return $this->articleHelper->getCategories();
    }

    private function getCategoryDetails(array $arguments): array
    {
        $categoryId = $arguments['category_id'] ?? null;
        if (! $categoryId) {
            return ['error' => 'ID de la catégorie manquant, veuillez refaire votre demande en incluant l\'ID de la catégorie.'];
        }

        $category = Category::find($categoryId);

        if (! $category) {
            return ['error' => 'Catégorie introuvable avec l\'ID fourni.'];
        }

        return [
            'success' => true,
            'category' => [
                'id_category' => $category->id_categorie,
                'name' => $category->nom_categorie,
                'full_path' => $category->getFullPath(),
            ],
        ];
    }

    private function buildOrderDetails(Order $order, bool $includeDetails = true): array
    {
        return [
            'order_number' => $order->num_commande,
            'date' => $order->date_commande->format('Y-m-d H:i:s'),
            'current_status' => $order->currentState()->label_etat,
            'status_history' => $includeDetails ? $order->states->map(fn ($state) => [
                'status' => $state->label_etat,
                'changed_at' => $state->pivot->date_changement,
            ])->toArray() : null,
            'total' => $order->items->sum(fn ($item) => $item->quantite_ligne * $item->prix_unit_ligne),
            'shipping_cost' => $order->frais_livraison,
            'click_and_collect_shop' => $includeDetails ? [
                'id' => $order->shop->id_magasin ?? null,
                'name' => $order->shop->nom_magasin ?? null,
                'address' => $order->shop ? [
                    'street' => $order->shop->full_address,
                    'city' => $order->shop->city->nom_ville,
                    'postal_code' => $order->shop->city->cp_ville,
                ] : null,
            ] : null,
            'shipping_mode' => $order->shippingMode->label_moyen_livraison,
            'tracking_number' => $order->num_suivi_commande,
            'items' => $includeDetails ? $order->items->map(fn ($item) => [
                'article_name' => $item->reference?->article->nom_article,
                'quantity' => $item->quantite_ligne,
                'unit_price' => $item->prix_unit_ligne,
                'total' => $item->quantite_ligne * $item->prix_unit_ligne,
            ])->toArray() : null,
            'delivery_address' => $includeDetails && $order->deliveryAddress ? [
                'id' => $order->deliveryAddress->id_adresse,
                'alias' => $order->deliveryAddress->alias_adresse,
                'name' => $order->deliveryAddress->nom_adresse,
                'street' => $order->deliveryAddress->rue_adresse,
                'city' => $order->deliveryAddress->city?->nom_ville,
                'postal_code' => $order->deliveryAddress->city?->postal_code,
            ] : null,
            'billing_address' => $includeDetails && $order->billingAddress ? [
                'id' => $order->billingAddress->id_adresse,
                'alias' => $order->billingAddress->alias_adresse,
                'name' => $order->billingAddress->nom_adresse,
                'street' => $order->billingAddress->rue_adresse,
                'city' => $order->billingAddress->city?->nom_ville,
                'postal_code' => $order->billingAddress->city?->postal_code,
            ] : null,
        ];
    }

    private function authenticatedUserError(): array
    {
        return ['error' => 'Utilisateur non connecté. Cette information nécessite une authentification.'];
    }
}

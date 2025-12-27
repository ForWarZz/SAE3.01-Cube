<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\Bike;
use App\Models\BikeReference;
use App\Models\Category;
use App\Models\Characteristic;
use App\Services\Cart\CartService;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CubeAssistantService
{
    private const GEMINI_MODEL = 'gemini-2.0-flash';

    private const SYSTEM_PROMPT_PATH = 'prompts/cube_assistant_system.txt';

    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function askGemini(string $message, string $pageType, ?int $contextId): string
    {
        try {
            $systemPrompt = $this->getSystemPrompt();
            $situationalContext = $this->buildSituationalContext($pageType, $contextId);

            $result = Gemini::generativeModel(model: self::GEMINI_MODEL)
                ->generateContent([
                    "SYSTEME : {$systemPrompt}",
                    "CONTEXTE SITUATIONNEL : {$situationalContext}",
                    "UTILISATEUR : {$message}",
                ]);

            $this->logPrompt($systemPrompt, $situationalContext, $message);

            return $result->text();

        } catch (\Exception $e) {
            Log::error('Gemini API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 'Désolé, je rencontre un problème technique momentané. Essayez de reformuler.';
        }
    }

    /**
     * @throws FileNotFoundException
     */
    private function getSystemPrompt(): string
    {
        $promptPath = resource_path(self::SYSTEM_PROMPT_PATH);

        if (File::exists($promptPath)) {
            return File::get($promptPath);
        }

        return 'Désolé, le fichier de prompt système est introuvable.';
    }

    private function buildSituationalContext(string $pageType, ?int $contextId): string
    {
        $context = [
            'metadata' => $this->buildMetadata($pageType, $contextId),
            'payload' => $this->buildPayload($pageType, $contextId),
        ];

        return json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function buildMetadata(string $pageType, ?int $contextId): array
    {
        return [
            'page_type' => $pageType,
            'context_id' => $contextId,
            'timestamp' => now()->toIso8601String(),
            'locale' => 'fr_FR',
            'currency' => 'EUR',
        ];
    }

    private function buildPayload(string $pageType, ?int $contextId): array
    {
        return match ($pageType) {
            'article-reference' => $this->buildArticleReferencePayload($contextId),
            'category' => $this->buildCategoryPayload($contextId),
            'cart' => $this->buildCartPayload(),
            'checkout' => $this->buildCheckoutPayload(),
            'profile' => $this->buildProfilePayload(),
            default => $this->buildGeneralPayload(),
        };
    }

    private function buildArticleReferencePayload(int $referenceId): array
    {
        $reference = $this->loadArticleReference($referenceId);

        $isAccessory = $reference->isAccessory();
        $variant = $reference->variant();
        $bike = $variant?->bike;
        $article = $reference->article;

        if (! $article) {
            return ['error' => 'Article not found'];
        }

        return [
            'type' => 'article_reference',
            'id' => $referenceId,
            'current_page' => 'FICHE PRODUIT',
            'user_location' => "L'utilisateur est actuellement sur la fiche produit détaillée de cet article. Il peut voir les photos, le prix, la description, les caractéristiques techniques et les variantes disponibles (couleurs, tailles).",
            'available_actions' => [
                'select_variant' => 'Sélectionner une couleur en cliquant sur la pastille de couleur',
                'select_size' => 'Sélectionner une taille dans le menu déroulant ou les boutons',
                'check_availability' => 'Cliquer sur le bouton "Voir les disponibilités" pour consulter les magasins',
                'add_to_cart' => 'Cliquer sur "Ajouter au panier" après avoir sélectionné les variantes',
                'view_accessories' => $bike ? 'Consulter l\'onglet "Accessoires compatibles"' : null,
                'view_similar' => 'Consulter la section "Articles similaires" en bas de page',
            ],
            'payload' => [
                'article_name' => $article->nom_article,
                'price' => $article->getDiscountedPrice(),
                'original_price' => $article->prix_article,
                'has_discount' => $article->hasDiscount(),
                'is_accessory' => $isAccessory,
                'is_ebike' => (bool) $bike?->ebike,
                'availability_online' => $reference->available_online ?? null,
                'characteristics' => $this->extractCharacteristics($article),
                'variants' => $bike ? $this->extractVariants($bike) : [],
                'compatible_accessories' => $bike ? $this->extractCompatibleAccessories($bike) : [],
                'similar_articles' => $this->extractSimilarArticles($article),
            ],
        ];
    }

    private function buildCategoryPayload(int $categoryId): array
    {
        $category = Category::find($categoryId)->load('parentRecursive');

        if (! $category) {
            return ['error' => 'Category not found'];
        }

        return [
            'type' => 'category',
            'id' => $categoryId,
            'current_page' => 'PAGE CATÉGORIE',
            'user_location' => "L'utilisateur navigue dans la catégorie \"{$category->nom_categorie}\". Il voit une liste d'articles avec leurs vignettes, noms et prix. Il peut utiliser les filtres pour affiner sa recherche.",
            'available_actions' => [
                'use_filters' => 'Utiliser les filtres latéraux (prix, taille, couleur, matériau, usage, etc.)',
                'sort_results' => 'Trier les résultats (prix croissant/décroissant, nouveautés, etc.)',
                'click_article' => 'Cliquer sur un article pour ouvrir sa fiche produit détaillée et voir sa disponibilité',
                'navigate_breadcrumb' => 'Utiliser le fil d\'Ariane pour remonter dans l\'arborescence',
                'view_subcategories' => 'Explorer les sous-catégories si disponibles',
            ],
            'important_notes' => [
                'La disponibilité détaillée (en ligne/magasin) n\'est PAS visible sur cette page',
                'Pour voir la disponibilité, l\'utilisateur DOIT cliquer sur un article pour ouvrir sa fiche',
                'Les badges "Promotion" et "Nouveauté" sont visibles sur les vignettes',
            ],
            'payload' => [
                'category_name' => $category->nom_categorie,
                'category_path' => $category->getFullPath(),
            ],
        ];
    }

    private function buildCartPayload(): array
    {
        $cartData = $this->cartService->getCartData();

        return [
            'type' => 'cart',
            'current_page' => 'PANIER',
            'user_location' => "L'utilisateur est dans son panier. Il voit la liste de ses articles avec les quantités, prix unitaires et le total de la commande.",
            'available_actions' => [
                'modify_quantity' => 'Modifier les quantités des articles (+ ou - ou saisie directe)',
                'remove_item' => 'Supprimer un article du panier (bouton "Supprimer")',
                'apply_promo' => 'Appliquer un code promo dans le champ "Code promo" puis cliquer "Appliquer"',
                'continue_shopping' => 'Continuer les achats (retour aux catégories)',
                'checkout' => 'Cliquer sur "Valider le panier" pour passer à la commande',
            ],
            'important_rules' => [
                'Livraison offerte en magasin revendeur à partir de 50€',
                'Click & Collect OBLIGATOIRE si un vélo est dans le panier',
                'Un seul code promo par commande',
                'Connexion obligatoire pour valider le panier',
            ],
            'payload' => [
                ...$cartData->toArray(),
            ],
        ];
    }

    private function buildCheckoutPayload(): array
    {
        $cartData = $this->cartService->getCartData();

        return [
            'type' => 'checkout',
            'current_page' => 'PAGE PAIEMENT',
            'user_location' => "L'utilisateur est en train de finaliser sa commande. Il saisit ses informations de livraison, choisit son mode de livraison et procède au paiement.",
            'available_actions' => [
                'fill_address' => 'Saisir l\'adresse de livraison',
                'select_billing_address' => 'Saisir l\'adresse de facturation (peut être différente)',
                'choose_delivery' => 'Choisir le mode de livraison (Livraison express, Click & Collect, Point relais)',
                'select_payment' => 'Choisir le moyen de paiement (CB, PayPal, Apple Pay, Google Pay)',
                'validate_order' => 'Cliquer sur "Payer" pour finaliser la commande',
            ],
            'important_rules' => [
                'Click & Collect obligatoire si un vélo est dans la commande',
                'Vérifier que l\'adresse de facturation correspond à celle de la banque',
                'Paiement sécurisé via Stripe',
            ],
            'assistant_behavior' => [
                'Aider uniquement pour problèmes techniques ou questions sur le processus',
                'NE PAS proposer de ventes additionnelles',
                'Être concis et efficace pour ne pas ralentir la conversion',
            ],
            'payload' => [
                ...$cartData->toArray(),
            ],
        ];
    }

    private function buildProfilePayload(): array
    {
        return [
            'type' => 'profile',
            'current_page' => 'PANNEAU DE PROFIL',
            'user_location' => "L'utilisateur est sur son panneau de profil. Il peut gérer son compte, ses adresses, consulter ses commandes.",
            'available_actions' => [
                'edit_profile' => 'Accéder au aux informations de votre compte client en cliquant sur la tuile "Profil", puis "Modifier" pour éditer les informations personnelles',
                'change_password' => 'Accéder au aux informations de votre compte client en cliquant sur la tuile "Profil", puis remplir le formulaire "Changer le mot de passe" (mot de passe actuel + nouveau + confirmation)',
                'enable_2fa' => 'Accéder au aux informations de votre compte client en cliquant sur la tuile "Profil", puis activer l\'A2F via "Activer la double authentification"',
                'view_orders' => 'Cliquer sur la tuile "Commandes" pour voir l\'historique',
                'manage_addresses' => 'Cliquer sur la tuile "Adresses" pour gérer les adresses',
                'delete_account' => 'Bas de page → "Supprimer mon compte" (confirmation par mot de passe requise)',
                'export_data' => 'Cliquer sur "Exporter mes données" (format JSON)',
            ],
            'important_notes' => [
                'L\'assistant NE PEUT PAS exécuter ces actions',
                'Il doit UNIQUEMENT guider l\'utilisateur étape par étape',
                'Ne jamais demander de mot de passe ou code A2F',
            ],
            'payload' => [],
        ];
    }

    private function buildGeneralPayload(): array
    {
        return [
            'type' => 'general',
            'current_page' => 'PAGE GÉNÉRALE',
            'user_location' => "L'utilisateur navigue sur le site Cube Bikes. Le contexte spécifique de la page n'est pas disponible.",
            'available_actions' => [
                'search' => 'Utiliser la barre de recherche',
                'browse_menu' => 'Explorer les catégories via le menu principal',
                'access_profile' => 'Accéder au profil (icône cycliste)',
                'access_cart' => 'Accéder au panier (icône panier)',
            ],
            'payload' => [
                'message' => 'Navigation générale sur le site. Proposer une aide générale ou demander plus de précisions sur ce que recherche l\'utilisateur ou l\'endroit où l\'utilisateur se trouve.',
            ],
        ];
    }

    private function loadArticleReference(int $referenceId): ArticleReference
    {
        return ArticleReference::with([
            'article.characteristics.characteristicType',
            'article.similar',
        ])->findOrFail($referenceId);
    }

    private function extractCharacteristics(Article $article): array
    {
        return $article->characteristics
            ->map(fn (Characteristic $charac) => [
                'type' => $charac->characteristicType->nom_type_carac,
                'value' => $charac->pivot->valeur_caracteristique,
            ])
            ->toArray();
    }

    private function extractVariants(Bike $bike): array
    {
        return $bike->references()
            ->with(['color', 'frame', 'article', 'ebike.battery'])
            ->get()
            ->map(fn (BikeReference $ref) => [
                'id_reference' => $ref->id_reference,
                'color' => $ref->color->label_couleur,
                'frame' => $ref->frame->label_cadre_velo,
                'price' => $ref->article->getDiscountedPrice(),
                'original_price' => $ref->article->prix_article,
                'battery' => $ref->ebike?->battery->label_batterie,
                'available_online' => $ref->available_online ?? null,
            ])
            ->toArray();
    }

    private function extractCompatibleAccessories(Bike $bike): array
    {
        return $bike->compatibleAccessories
            ->map(fn ($accessory) => [
                'name' => $accessory->nom_article,
                'price' => $accessory->getDiscountedPrice(),
                'original_price' => $accessory->prix_article,
                'id_article' => $accessory->id_article,
            ])
            ->toArray();
    }

    private function extractSimilarArticles(Article $article): array
    {
        return $article->similar
            ->map(fn ($similar) => [
                'name' => $similar->nom_article,
                'price' => $similar->getDiscountedPrice(),
                'original_price' => $similar->prix_article,
                'id_article' => $similar->id_article,
            ])
            ->toArray();
    }

    private function logPrompt(string $systemPrompt, string $situationalContext, string $userMessage): void
    {
        Log::info('Gemini Prompt Sent', [
            'system_prompt_length' => strlen($systemPrompt),
            'situational_context' => json_decode($situationalContext, true),
            'user_message' => $userMessage,
        ]);
    }
}

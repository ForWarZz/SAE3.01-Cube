<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\Bike;
use App\Models\BikeReference;
use App\Models\Category;
use App\Models\Characteristic;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CubeAssistantService
{
    private const GEMINI_MODEL = 'gemini-2.0-flash';

    private const SYSTEM_PROMPT_PATH = 'prompts/cube_assistant_system.txt';

    public function askGemini(string $message, string $pageType, ?int $contextId): string
    {
        try {
            $systemPrompt = $this->getSystemPrompt();
            $situationalContext = $this->buildSituationalContext($pageType, $contextId);

            $result = Gemini::generativeModel(model: self::GEMINI_MODEL)
                ->generateContent([
                    "SYST\u00c8ME : {$systemPrompt}",
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

            return 'D\u00e9sol\u00e9, je rencontre un probl\u00e8me technique momentan\u00e9. Essayez de reformuler.';
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

        return 'D\u00e9sol\u00e9, le fichier de prompt syst\u00e8me est introuvable.';
    }

    private function buildSituationalContext(string $pageType, ?int $contextId): string
    {
        $context = [
            'metadata' => $this->buildMetadata($pageType, $contextId),
            'site_capabilities' => $this->getSiteCapabilities(),
            'instructions' => $this->getContextInstructions(),
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

    private function getSiteCapabilities(): array
    {
        return [
            'search' => true,
            'filters' => [
                'category', 'price', 'size', 'color',
                'frame_material', 'usage', 'bike_model',
                'vintage', 'availability', 'promotion',
            ],
            'click_and_collect' => true,
            'home_delivery' => true,
            'payment_methods' => ['CB', 'PayPal', 'ApplePay', 'GooglePay', 'Stripe'],
        ];
    }

    private function getContextInstructions(): array
    {
        return [
            'truth_source' => 'Use ONLY data in payload. If missing, respond: "Je n\'ai pas cette information pour le moment."',
            'no_hallucination' => true,
            'check_current_page' => 'ALWAYS check current_page and user_location before responding to avoid redirecting user to pages they are already on',
            'format' => 'markdown',
            'blank_line_before_lists' => true,
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
            'user_location' => "L'utilisateur est actuellement sur la fiche produit d\u00e9taill\u00e9e de cet article. Il peut voir les photos, le prix, la description, les caract\u00e9ristiques techniques et les variantes disponibles (couleurs, tailles).",
            'available_actions' => [
                'select_variant' => 'S\u00e9lectionner une couleur en cliquant sur la pastille de couleur',
                'select_size' => 'S\u00e9lectionner une taille dans le menu d\u00e9roulant ou les boutons',
                'check_availability' => 'Cliquer sur le bouton "Voir les disponibilit\u00e9s" pour consulter les magasins',
                'add_to_cart' => 'Cliquer sur "Ajouter au panier" apr\u00e8s avoir s\u00e9lectionn\u00e9 les variantes',
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
        $category = Category::find($categoryId);

        if (! $category) {
            return ['error' => 'Category not found'];
        }

        return [
            'type' => 'category',
            'id' => $categoryId,
            'current_page' => 'PAGE CAT\u00c9GORIE',
            'user_location' => "L'utilisateur navigue dans la cat\u00e9gorie \"{$category->nom_categorie}\". Il voit une liste d'articles avec leurs vignettes, noms et prix. Il peut utiliser les filtres pour affiner sa recherche.",
            'available_actions' => [
                'use_filters' => 'Utiliser les filtres lat\u00e9raux (prix, taille, couleur, mat\u00e9riau, usage, etc.)',
                'sort_results' => 'Trier les r\u00e9sultats (prix croissant/d\u00e9croissant, nouveaut\u00e9s, etc.)',
                'click_article' => 'Cliquer sur un article pour ouvrir sa fiche produit d\u00e9taill\u00e9e et voir sa disponibilit\u00e9',
                'navigate_breadcrumb' => 'Utiliser le fil d\'Ariane pour remonter dans l\'arborescence',
                'view_subcategories' => 'Explorer les sous-cat\u00e9gories si disponibles',
            ],
            'important_notes' => [
                'La disponibilit\u00e9 d\u00e9taill\u00e9e (en ligne/magasin) n\'est PAS visible sur cette page',
                'Pour voir la disponibilit\u00e9, l\'utilisateur DOIT cliquer sur un article pour ouvrir sa fiche',
                'Les badges "Promotion" et "Nouveaut\u00e9" sont visibles sur les vignettes',
            ],
            'payload' => [
                'category_name' => $category->nom_categorie,
                'category_description' => $category->description_categorie ?? null,
            ],
        ];
    }

    private function buildCartPayload(): array
    {
        return [
            'type' => 'cart',
            'current_page' => 'PANIER',
            'user_location' => "L'utilisateur est dans son panier. Il voit la liste de ses articles avec les quantit\u00e9s, prix unitaires et le total de la commande.",
            'available_actions' => [
                'modify_quantity' => 'Modifier les quantit\u00e9s des articles (+ ou - ou saisie directe)',
                'remove_item' => 'Supprimer un article du panier (bouton "Supprimer")',
                'apply_promo' => 'Appliquer un code promo dans le champ "Code promo" puis cliquer "Appliquer"',
                'continue_shopping' => 'Continuer les achats (retour aux cat\u00e9gories)',
                'checkout' => 'Cliquer sur "Valider le panier" pour passer \u00e0 la commande',
            ],
            'important_rules' => [
                'Livraison offerte en magasin revendeur \u00e0 partir de 50\u20ac',
                'Click & Collect OBLIGATOIRE si un v\u00e9lo est dans le panier',
                'Un seul code promo par commande',
                'Connexion obligatoire pour valider le panier',
            ],
            'payload' => [

            ],
        ];
    }

    private function buildCheckoutPayload(): array
    {
        return [
            'type' => 'checkout',
            'current_page' => 'PAGE PAIEMENT',
            'user_location' => "L'utilisateur est en train de finaliser sa commande. Il saisit ses informations de livraison, choisit son mode de livraison et proc\u00e8de au paiement.",
            'available_actions' => [
                'fill_address' => 'Saisir l\'adresse de livraison',
                'select_billing_address' => 'Saisir l\'adresse de facturation (peut \u00eatre diff\u00e9rente)',
                'choose_delivery' => 'Choisir le mode de livraison (Livraison express, Click & Collect, Point relais)',
                'select_payment' => 'Choisir le moyen de paiement (CB, PayPal, Apple Pay, Google Pay)',
                'validate_order' => 'Cliquer sur "Payer" pour finaliser la commande',
            ],
            'important_rules' => [
                'Click & Collect obligatoire si un v\u00e9lo est dans la commande',
                'V\u00e9rifier que l\'adresse de facturation correspond \u00e0 celle de la banque',
                'Paiement s\u00e9curis\u00e9 via Stripe',
            ],
            'assistant_behavior' => [
                'Aider uniquement pour probl\u00e8mes techniques ou questions sur le processus',
                'NE PAS proposer de ventes additionnelles',
                '\u00catre concis et efficace pour ne pas ralentir la conversion',
            ],
            'payload' => [],
        ];
    }

    private function buildProfilePayload(): array
    {
        return [
            'type' => 'profile',
            'current_page' => 'PANNEAU DE PROFIL',
            'user_location' => "L'utilisateur est sur son panneau de profil. Il peut g\u00e9rer son compte, ses adresses, consulter ses commandes.",
            'available_actions' => [
                'edit_profile' => 'Acc\u00e9der au aux informations de votre compte client en cliquant sur la tuile "Profil", puis "Modifier" pour \u00e9diter les informations personnelles',
                'change_password' => 'Acc\u00e9der au aux informations de votre compte client en cliquant sur la tuile "Profil", puis remplir le formulaire "Changer le mot de passe" (mot de passe actuel + nouveau + confirmation)',
                'enable_2fa' => 'Acc\u00e9der au aux informations de votre compte client en cliquant sur la tuile "Profil", puis activer l\'A2F via "Activer la double authentification"',
                'view_orders' => 'Cliquer sur la tuile "Commandes" pour voir l\'historique',
                'manage_addresses' => 'Cliquer sur la tuile "Adresses" pour g\u00e9rer les adresses',
                'delete_account' => 'Bas de page \u2192 "Supprimer mon compte" (confirmation par mot de passe requise)',
                'export_data' => 'Cliquer sur "Exporter mes donn\u00e9es" (format JSON)',
            ],
            'important_notes' => [
                'L\'assistant NE PEUT PAS ex\u00e9cuter ces actions',
                'Il doit UNIQUEMENT guider l\'utilisateur \u00e9tape par \u00e9tape',
                'Ne jamais demander de mot de passe ou code A2F',
            ],
            'payload' => [],
        ];
    }

    private function buildGeneralPayload(): array
    {
        return [
            'type' => 'general',
            'current_page' => 'PAGE G\u00c9N\u00c9RALE',
            'user_location' => "L'utilisateur navigue sur le site Cube Bikes. Le contexte sp\u00e9cifique de la page n'est pas disponible.",
            'available_actions' => [
                'search' => 'Utiliser la barre de recherche',
                'browse_menu' => 'Explorer les cat\u00e9gories via le menu principal',
                'access_profile' => 'Acc\u00e9der au profil (ic\u00f4ne cycliste)',
                'access_cart' => 'Acc\u00e9der au panier (ic\u00f4ne panier)',
            ],
            'payload' => [
                'message' => 'Navigation g\u00e9n\u00e9rale sur le site. Proposer une aide g\u00e9n\u00e9rale ou demander plus de pr\u00e9cisions sur ce que recherche l\'utilisateur ou l\'endroit o\u00f9 l\'utilisateur se trouve.',
            ],
        ];
    }

    private function loadArticleReference(int $referenceId): ArticleReference
    {
        // Utiliser le scope centralisé pour s'assurer que les relations principales sont eager-loaded
        return ArticleReference::withFullRelations()
            ->with([
                // Relations complémentaires spécifiques au CubeAssistant
                'accessory.article.characteristics.characteristicType',
                'accessory.article.similar',
                'accessory.material',
                'accessory.shopAvailabilities',

                // S'assurer que la relation bikeReference.bike est présente
                'bikeReference.bike',

                // Garantir les tailles et disponibilités sur la bikeReference
                'bikeReference.shopAvailabilities',
                'bikeReference.availableSizes',
            ])
            ->findOrFail($referenceId);
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

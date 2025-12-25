<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\Bike;
use App\Models\BikeReference;
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
                    "SYSTÈME : {$systemPrompt}",
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
            'payment_methods' => ['CB', 'PayPal', 'ApplePay', 'Stripe'],
        ];
    }

    private function getContextInstructions(): array
    {
        return [
            'truth_source' => 'Use ONLY data in payload. If missing, respond: "Je n\'ai pas cette information pour le moment."',
            'no_hallucination' => true,
            'format' => 'markdown',
            'blank_line_before_lists' => true,
        ];
    }

    private function buildPayload(string $pageType, ?int $contextId): array
    {
        return match ($pageType) {
            'article-reference' => $this->buildArticleReferencePayload($contextId),
            'cart' => $this->buildCartPayload(),
            'checkout' => $this->buildCheckoutPayload(),
            default => $this->buildGeneralPayload(),
        };
    }

    private function buildArticleReferencePayload(int $referenceId): array
    {
        $reference = $this->loadArticleReference($referenceId);

        $isAccessory = (bool) $reference->accessory;
        $bikeReference = $reference->bikeReference;
        $bike = $bikeReference?->bike;
        $article = $bikeReference?->article ?? $reference->accessory?->article;

        if (! $article) {
            return ['error' => 'Article not found'];
        }

        return [
            'type' => 'article_reference',
            'id' => $referenceId,
            'payload' => [
                'is_accessory' => $isAccessory,
                'is_ebike' => (bool) $bike?->ebike,
                'characteristics' => $this->extractCharacteristics($article),
                'variants' => $bike ? $this->extractVariants($bike) : [],
                'compatible_accessories' => $bike ? $this->extractCompatibleAccessories($bike) : [],
                'similar_articles' => $this->extractSimilarArticles($article),
            ],
        ];
    }

    private function loadArticleReference(int $referenceId): ArticleReference
    {
        return ArticleReference::with([
            'accessory.article.characteristics.characteristicType',
            'accessory.article.similar',
            'accessory.material',
            'accessory.shopAvailabilities',
            'bikeReference.article.characteristics.characteristicType',
            'bikeReference.bike',
            'bikeReference.color',
            'bikeReference.frame',
            'bikeReference.article.similar',
            'bikeReference.shopAvailabilities',
            'bikeReference.availableSizes',
            'bikeReference.bike.compatibleAccessories',
            'bikeReference.bike.similar',
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
                'battery' => $ref->ebike?->battery->label_batterie,
            ])
            ->toArray();
    }

    private function extractCompatibleAccessories(Bike $bike): array
    {
        return $bike->compatibleAccessories
            ->map(fn ($accessory) => [
                'name' => $accessory->nom_article,
                'price' => $accessory->prix_article,
                'id_article' => $accessory->id_article,
            ])
            ->toArray();
    }

    private function extractSimilarArticles(Article $article): array
    {
        return $article->similar
            ->map(fn ($similar) => [
                'name' => $similar->nom_article,
                'price' => $similar->prix_article,
                'id_article' => $similar->id_article,
            ])
            ->toArray();
    }

    private function buildCartPayload(): array
    {
        return [
            'type' => 'cart',
            'notes' => [
                'Livraison offerte en magasin revendeur à partir de 50€.',
                'Click & Collect obligatoire si un vélo est dans le panier.',
                'Champ code promo disponible dans le panier.',
            ],
        ];
    }

    private function buildCheckoutPayload(): array
    {
        return [
            'type' => 'checkout',
            'notes' => [
                'Utilisateur en train de payer. Aider uniquement pour problèmes techniques.',
                'Ne pas proposer de ventes additionnelles.',
            ],
        ];
    }

    private function buildGeneralPayload(): array
    {
        return [
            'type' => 'general',
            'message' => 'Navigation générale sur le site.',
        ];
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

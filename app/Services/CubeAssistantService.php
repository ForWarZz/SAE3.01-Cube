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
    private const GEMINI_MODEL = 'gemini-2.5-flash';

    private const SYSTEM_PROMPT_PATH = 'prompts/cube_assistant_system.txt';

    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function askGemini(string $message, string $pageType, string $pageUrl, ?int $contextId): string
    {
        try {
            $systemPrompt = $this->getSystemPrompt();
            $situationalContext = $this->buildSituationalContext($pageType, $pageUrl, $contextId);

            $result = Gemini::generativeModel(model: self::GEMINI_MODEL)
                ->generateContent([
                    "SYSTEME : {$systemPrompt}",
                    "CONTEXTE SITUATIONNEL (JSON) : {$situationalContext}",
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

    private function buildSituationalContext(string $pageType, string $pageUrl, ?int $contextId): string
    {
        $context = [
            'metadata' => $this->buildMetadata($pageType, $pageUrl, $contextId),
            'data' => $this->buildPayload($pageType, $contextId),
        ];

        return json_encode($context, JSON_UNESCAPED_UNICODE);
    }

    private function buildMetadata(string $pageType, string $pageUrl, ?int $contextId): array
    {
        return [
            'page_type' => $pageType,
            'context_id' => $contextId,
            'page_url' => $pageUrl,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function buildPayload(string $pageType, ?int $contextId): array
    {
        return match ($pageType) {
            'article-reference' => $this->buildArticleReferencePayload($contextId),
            'category' => $this->buildCategoryPayload($contextId),
            'cart', 'checkout' => $this->buildCartPayload(),
            'profile' => $this->buildProfilePayload(),
            default => [],
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
        ];
    }

    private function buildCategoryPayload(int $categoryId): array
    {
        $category = Category::find($categoryId)->load('parentRecursive');

        if (! $category) {
            return ['error' => 'Category not found'];
        }

        return [
            'category_name' => $category->nom_categorie,
            'category_path' => $category->getFullPath(),
        ];
    }

    private function buildCartPayload(): array
    {
        $cartData = $this->cartService->getCartData();

        return $cartData->toArray();
    }

    private function buildProfilePayload(): array
    {
        $client = auth()->user();

        return [
            'client_name' => $client->prenom_client,
            'client_email' => $client->email_client,
            'has_2fa_enabled' => (bool) $client->two_factor_confirmed_at,
            'is_google_authenticated' => (bool) $client->google_id,
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
            ->collapse()
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

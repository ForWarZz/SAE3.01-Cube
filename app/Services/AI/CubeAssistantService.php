<?php

namespace App\Services\AI;

use App\Models\Category;
use App\Services\Cart\CartService;
use Exception;
use Gemini\Data\Content;
use Gemini\Data\FunctionResponse;
use Gemini\Data\Part;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CubeAssistantService
{
    private const GEMINI_MODEL = 'gemini-2.5-flash';

    private const SYSTEM_PROMPT_PATH = 'prompts/cube_assistant_system.txt';

    public function __construct(
        private readonly GeminiFunctionExecutor $functionExecutor,
        private readonly ReferenceDataService $referenceDataService
    ) {}

    public function askGemini(string $message, string $pageType, string $pageUrl, ?array $context): string
    {
        try {
            $systemPrompt = $this->getSystemPrompt();
            $situationalContext = $this->buildSituationalContext($pageType, $pageUrl, $context);
            $referenceData = $this->referenceDataService->getReferenceData();

            $this->logPrompt($systemPrompt, $situationalContext, $message);

            $tools = GeminiFunctionRegistry::getTools();

            $chat = Gemini::generativeModel(model: self::GEMINI_MODEL);
            $chat->tools = $tools;

            $chat = $chat->startChat([
                new Content(
                    parts: [
                        new Part(text: $systemPrompt),
                        new Part(text: json_encode([
                            'reference_data' => $referenceData,
                        ], JSON_UNESCAPED_UNICODE)),
                    ],
                    role: Role::MODEL
                ),
                new Content(
                    parts: [
                        new Part(text: json_encode([
                            'context' => json_decode($situationalContext, true),
                            'question' => $message,
                        ], JSON_UNESCAPED_UNICODE)),
                    ],
                    role: Role::USER
                ),
            ]);

            $response = $chat->sendMessage('Analyse et réponds.');

            Log::info('Response received', [
                'parts' => $response->parts(),
            ]);

            if ($response->parts()[0]->functionCall !== null) {
                $functionCall = $response->parts()[0]->functionCall;
                $thoughtSignature = $response->parts()[0]->thoughtSignature;

                $functionResult = $this->functionExecutor->execute(
                    $functionCall->name,
                    $functionCall->args
                );

                $parts = [
                    new Part(
                        functionResponse: new FunctionResponse(
                            name: $functionCall->name,
                            response: $functionResult
                        ),
                        thoughtSignature: $thoughtSignature,
                    ),
                ];

                $content = new Content(
                    parts: $parts,
                    role: Role::USER
                );

                $response = $chat->sendMessage($content);
            }

            return $response->text();
        } catch (Exception $exception) {
            Log::warning($exception->getMessage());

            return 'Désolé, une erreur est survenue lors du traitement de votre demande. Les services de Gemini ne sont pas disponibles pour le moment.';
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

    private function buildSituationalContext(string $pageType, string $pageUrl, ?array $context): string
    {
        $context = [
            'metadata' => $this->buildMetadata($pageType, $pageUrl, $context),
            //            'data' => $this->buildPayload($pageType, $context),
        ];

        return json_encode($context, JSON_UNESCAPED_UNICODE);
    }

    private function buildMetadata(string $pageType, string $pageUrl, ?array $context): array
    {
        return [
            'page_type' => $pageType,
            'context' => $context,
            'page_url' => $pageUrl,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    //    private function buildPayload(string $pageType, ?array $context): array
    //    {
    //        return match ($pageType) {
    //            'article-reference' => $this->buildArticleReferencePayload($contextId),
    //            'category' => $this->buildCategoryPayload($contextId),
    //            'cart', 'checkout' => $this->buildCartPayload(),
    //            default => [],
    //        };
    //    }
    //
    //    private function buildArticleReferencePayload(int $referenceId): array
    //    {
    //        // L'IA utilisera les outils pour récupérer les informations nécessaires
    //        return [
    //            'page_type' => 'article_reference',
    //            'reference_id' => $referenceId,
    //        ];
    //    }
    //
    //    private function buildCategoryPayload(int $categoryId): array
    //    {
    //        $category = Category::find($categoryId)->load('parentRecursive');
    //
    //        if (! $category) {
    //            return ['error' => 'Category not found'];
    //        }
    //
    //        return [
    //            'category_name' => $category->nom_categorie,
    //            'category_path' => $category->getFullPath(),
    //        ];
    //    }
    //
    //    private function buildCartPayload(): array
    //    {
    //        $cartData = $this->cartService->getCartData();
    //
    //        return $cartData->toArray();
    //    }

    private function logPrompt(string $systemPrompt, string $situationalContext, string $userMessage): void
    {
        Log::info('Gemini Prompt Sent', [
            'system_prompt_length' => strlen($systemPrompt),
            'situational_context' => json_decode($situationalContext, true),
            'user_message' => $userMessage,
        ]);
    }
}

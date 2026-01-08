<?php

namespace App\Services\AI;

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

            $maxIterations = 5;
            $iteration = 0;

            while ($iteration < $maxIterations) {
                $iteration++;

                $parts = $response->parts();

                if (empty($parts) || $parts[0]->functionCall === null) {
                    break;
                }

                $functionCall = $parts[0]->functionCall;
                $thoughtSignature = $parts[0]->thoughtSignature;

                Log::info('Gemini function call', [
                    'name' => $functionCall->name,
                    'args' => $functionCall->args,
                ]);

                $functionResult = $this->functionExecutor->execute(
                    $functionCall->name,
                    $functionCall->args
                );

                $content = new Content(
                    parts: [
                        new Part(
                            functionResponse: new FunctionResponse(
                                name: $functionCall->name,
                                response: $functionResult
                            ),
                            thoughtSignature: $thoughtSignature
                        ),
                    ],
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

    private function logPrompt(string $systemPrompt, string $situationalContext, string $userMessage): void
    {
        Log::info('Gemini Prompt Sent', [
            'system_prompt_length' => strlen($systemPrompt),
            'situational_context' => json_decode($situationalContext, true),
            'user_message' => $userMessage,
        ]);
    }
}

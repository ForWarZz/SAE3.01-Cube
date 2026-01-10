<?php

namespace App\Services\AI;

use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Enums\Role;
use Illuminate\Support\Facades\Session;

class ConversationHistoryService
{
    private const SESSION_KEY = 'gemini_conversation_history';

    private const MAX_MESSAGES = 10;

    public function getHistory(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function addUserMessage(string $message): void
    {
        $history = $this->getHistory();

        $history[] = [
            'role' => 'user',
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->pruneHistory($history);
        Session::put(self::SESSION_KEY, $history);
    }

    public function addModelResponse(string $response): void
    {
        $history = $this->getHistory();

        $history[] = [
            'role' => 'model',
            'message' => $response,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->pruneHistory($history);
        Session::put(self::SESSION_KEY, $history);
    }

    public function toGeminiContents(): array
    {
        $history = $this->getHistory();
        $contents = [];

        foreach ($history as $message) {
            $role = $message['role'] === 'user' ? Role::USER : Role::MODEL;

            $contents[] = new Content(
                parts: [new Part(text: $message['message'])],
                role: $role
            );
        }

        return $contents;
    }

    private function pruneHistory(array &$history): void
    {
        if (count($history) > self::MAX_MESSAGES) {
            $history = array_slice($history, -self::MAX_MESSAGES);
        }
    }

    public function hasHistory(): bool
    {
        return ! empty($this->getHistory());
    }
}

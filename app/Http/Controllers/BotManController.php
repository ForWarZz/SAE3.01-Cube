<?php

namespace App\Http\Controllers;

use App\Services\CubeAssistantService;
use BotMan\BotMan\BotMan;

class BotManController extends Controller
{
    public function __construct(
        private readonly CubeAssistantService $aiService
    ) {}

    public function handle()
    {
        $botman = app('botman');

        $botman->fallback(function (BotMan $bot) {
            $bot->typesAndWaits(1);
            $message = $bot->getMessage()->getText();

            $aiResponse = $this->aiService->askGemini($message);

            $bot->reply($aiResponse);
        });

        $botman->listen();
    }
}

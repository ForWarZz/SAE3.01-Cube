<?php

namespace App\Http\Controllers;

use App\Services\CubeAssistantService;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class BotManController extends Controller
{
    public function __construct(
        private readonly CubeAssistantService $assistantService
    ) {}

    public function handle(Request $request)
    {
        $botman = app('botman');

        $botman->fallback(function (BotMan $bot) use ($request) {
            $bot->typesAndWaits(2);
            $message = $bot->getMessage()->getText();
            $pageType = $request->input('page_type', 'general');
            $contextId = $request->input('context_id');
            $pageUrl = $request->input('page_url');

            Log::info('BotMan received message: '.$message.' | page_type: '.$pageType.' | page_url: '.$pageUrl.' | context_id: '.$contextId);

            $response = $this->assistantService->askGemini($message, $pageType, $pageUrl, $contextId);
            $htmlResponse = Str::markdown($response);

            $bot->reply($htmlResponse);
        });

        $botman->listen();
    }
}

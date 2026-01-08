<?php

namespace App\Http\Controllers;

use App\Services\AI\CubeAssistantService;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class BotManController extends Controller
{
    public function __construct(
        private readonly CubeAssistantService $assistantService
    ) {}

    public function handle(Request $request, BotMan $botman)
    {
        $botman->fallback(function (BotMan $bot) use ($request) {
            $bot->typesAndWaits(2);
            $message = $bot->getMessage()->getText();
            $pageType = $request->input('page_type', 'general');
            $context = json_decode(urldecode($request->get('context')), true);
            $pageUrl = $request->input('page_url');

            Log::info('BotMan received message: '.$message.' | page_type: '.$pageType.' | page_url: '.$pageUrl.' | context: '.json_encode($context));

            $response = $this->assistantService->askGemini($message, $pageType, $pageUrl, $context);
            $htmlResponse = Str::markdown($response);

            $bot->reply($htmlResponse);
        });

        $botman->listen();
    }
}

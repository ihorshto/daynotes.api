<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

readonly class AnswerCallbackQuery
{
    private string $token;

    public function __construct()
    {
        $this->token = config('services.telegram-bot-api.token');
    }

    public function execute(string $callbackQueryId, string $text = ''): void
    {
        $payload = ['callback_query_id' => $callbackQueryId];

        if ($text !== '') {
            $payload['text'] = $text;
        }

        $response = Http::post(
            sprintf('https://api.telegram.org/bot%s/answerCallbackQuery', $this->token),
            $payload
        );

        if ($response->failed()) {
            Log::warning('Failed to answer callback query', [
                'callback_query_id' => $callbackQueryId,
                'status'            => $response->status(),
                'body'              => $response->body(),
            ]);
        }
    }
}

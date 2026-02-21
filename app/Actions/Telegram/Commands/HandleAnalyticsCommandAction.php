<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

readonly class HandleAnalyticsCommandAction
{
    public function handle(array $message): void
    {
        Log::info('url: '.config('services.n8n.url').'/webhook/mood-analytics');
        $response = Http::post(
            config('services.n8n.url').'/webhook/mood-analytics',
            []
        );

        Log::info('response full:', [
            'status' => $response->status(),
            'body'   => $response->body(),
            'json'   => $response->json(),
        ]);
    }
}

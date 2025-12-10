<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\TelegramController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:polling';

    protected $description = 'Receive messages from Telegram via polling';

    private int $offset = 0;

    public function handle(): void
    {
        $this->info('🚀 Starting Telegram polling...');
        $this->info('Press Ctrl+C to stop');

        $token = config('services.telegram-bot-api.token');

        // Disable webhook (because polling and webhook don't work together)
        Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

        $this->info('Webhook disabled, polling active');

        while (true) {
            try {
                // Request new messages from Telegram
                $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates", [
                    'offset' => $this->offset,
                    'timeout' => 60, // Wait 60 seconds for new messages
                ]);

                $updates = $response->json('result', []);

                foreach ($updates as $update) {
                    $this->info('📨 New message: '.json_encode($update));

                    // Process each message
                    $this->processUpdate($update);

                    // Save offset to avoid receiving the same message again
                    $this->offset = $update['update_id'] + 1;
                }

            } catch (\Exception $e) {
                $this->error('Error: '.$e->getMessage());
                sleep(5);
            }
        }
    }

    private function processUpdate(array $update): void
    {
        // Create fake request with data from Telegram
        $request = Request::create('/api/telegram/webhook', 'POST', $update);

        // Call the same webhook method
        $controller = app(TelegramController::class);
        $controller->webhook($request);
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use App\Actions\Telegram\WebhookAction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

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
        Http::post(sprintf('https://api.telegram.org/bot%s/deleteWebhook', $token));

        $this->info('Webhook disabled, polling active');

        while (true) {
            try {
                // Request new messages from Telegram
                $response = Http::get(sprintf('https://api.telegram.org/bot%s/getUpdates', $token), [
                    'offset'  => $this->offset,
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

            } catch (Exception $e) {
                $this->error('Error: '.$e->getMessage());
                Sleep::sleep(5);
            }
        }
    }

    private function processUpdate(array $update): void
    {
        // Create fake request with data from Telegram
        $request = Request::create('/api/telegram/webhook', 'POST', $update);

        // Call the same webhook method
        $controller = resolve(WebhookAction::class);
        $controller->execute($request);
    }
}

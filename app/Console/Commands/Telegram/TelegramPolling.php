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
    protected $signature = 'telegram:polling {--timeout=60 : Polling timeout in seconds}';

    protected $description = 'Receive messages from Telegram via polling';

    private int $offset = 0;

    private bool $shouldKeepRunning = true;

    private int $errorCount = 0;

    public function handle(): void
    {
        $this->info('Starting Telegram polling...');
        $this->info('Press Ctrl+C to stop');

        $timeout = (int) $this->option('timeout');
        $this->info(sprintf('Polling timeout: %d seconds', $timeout));

        // Register signal handlers for graceful shutdown
        $this->trap([SIGTERM, SIGINT], function (): void {
            $this->shouldKeepRunning = false;
            $this->info('Gracefully shutting down...');
        });

        $token = config('services.telegram-bot-api.token');

        // Disable webhook (because polling and webhook don't work together)
        Http::post(sprintf('https://api.telegram.org/bot%s/deleteWebhook', $token));

        $this->info('Webhook disabled, polling active');

        while ($this->shouldKeepRunning) {
            try {
                // Request new messages from Telegram
                $response = Http::get(sprintf('https://api.telegram.org/bot%s/getUpdates', $token), [
                    'offset'  => $this->offset,
                    'timeout' => $timeout,
                ]);

                $updates = $response->json('result', []);

                foreach ($updates as $update) {
                    $this->info("\n New message: ".json_encode($update));

                    // Process each message
                    $this->processUpdate($update);

                    // Save offset to avoid receiving the same message again
                    $this->offset = $update['update_id'] + 1;
                }

                // Reset error count on successful poll
                $this->errorCount = 0;

            } catch (Exception $e) {
                $this->errorCount++;

                // Exponential backoff: 2, 4, 8, 16, 32, 60 (max)
                $sleepTime = min(60, pow(2, $this->errorCount));

                $this->error('Error: '.$e->getMessage());
                $this->warn(sprintf('Retrying in %s seconds... (attempt %d)', $sleepTime, $this->errorCount));

                Sleep::sleep((int) $sleepTime);
            }
        }

        $this->info('Telegram polling stopped gracefully');
    }

    private function processUpdate(array $update): void
    {
        // Create fake request with data from Telegram
        $request = Request::create(route('telegram.webhook'), 'POST', $update);

        // Call the same webhook method
        $controller = resolve(WebhookAction::class);
        $controller->execute($request);
    }
}

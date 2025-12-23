<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;

class SetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {--url= : Custom webhook URL (optional)}';

    protected $description = 'Set up Telegram webhook for receiving bot updates';

    public function handle(): int
    {
        $token = config('services.telegram-bot-api.token');

        if (! $token) {
            $this->error('❌ Telegram bot token is not configured!');
            $this->info('Please set TELEGRAM_BOT_TOKEN in your .env file');

            return self::FAILURE;
        }

        // Get webhook URL (from option or generate from APP_URL)
        $webhookUrl = $this->option('url') ?? route('telegram.webhook');

        $this->info('🔄 Setting up Telegram webhook...');
        $this->info('Webhook URL: '.$webhookUrl);

        // Call Telegram API to set webhook
        $response = Http::post(sprintf('https://api.telegram.org/bot%s/setWebhook', $token), [
            'url' => $webhookUrl,
        ]);

        if ($response->successful() && $response->json('ok')) {
            $this->info('✅ Webhook successfully set!');
            $this->newLine();

            // Get webhook info to confirm
            $infoResponse = Http::get(sprintf('https://api.telegram.org/bot%s/getWebhookInfo', $token));

            if ($infoResponse->successful()) {
                $info = $infoResponse->json('result');
                $this->info('📋 Webhook Information:');
                $this->table(
                    ['Property', 'Value'],
                    [
                        ['URL', $info['url'] ?? 'N/A'],
                        ['Pending Updates', $info['pending_update_count'] ?? 0],
                        ['Last Error Date', isset($info['last_error_date']) ? Date::createFromTimestamp($info['last_error_date'])->format('Y-m-d H:i:s') : 'None'],
                        ['Last Error Message', $info['last_error_message'] ?? 'None'],
                    ]
                );
            }

            return self::SUCCESS;
        }

        $this->error('❌ Failed to set webhook');
        $errorDescription = $response->json('description', 'Unknown error');
        $this->error('Error: '.$errorDescription);

        return self::FAILURE;
    }
}

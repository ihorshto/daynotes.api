<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;

class WebhookInfo extends Command
{
    protected $signature = 'telegram:webhook-info';

    protected $description = 'Get current Telegram webhook information';

    public function handle(): int
    {
        $token = config('services.telegram-bot-api.token');

        if (! $token) {
            $this->error('❌ Telegram bot token is not configured!');
            $this->info('Please set TELEGRAM_BOT_TOKEN in your .env file');

            return self::FAILURE;
        }

        $this->info('🔄 Fetching webhook information...');

        // Call Telegram API to get webhook info
        $response = Http::get(sprintf('https://api.telegram.org/bot%s/getWebhookInfo', $token));

        if ($response->successful() && $response->json('ok')) {
            $info = $response->json('result');

            $this->newLine();
            $this->info('📋 Webhook Information:');

            if (empty($info['url'])) {
                $this->warn('⚠️  No webhook is currently set');
                $this->info('Use "php artisan telegram:set-webhook" to set up a webhook');
                $this->info('Or use "php artisan telegram:polling" to receive updates via polling');
            } else {
                $this->table(
                    ['Property', 'Value'],
                    [
                        ['URL', $info['url']],
                        ['Has Custom Certificate', $info['has_custom_certificate'] ? 'Yes' : 'No'],
                        ['Pending Update Count', $info['pending_update_count'] ?? 0],
                        ['Last Error Date', isset($info['last_error_date']) ? Date::createFromTimestamp($info['last_error_date'])->format('Y-m-d H:i:s') : 'None'],
                        ['Last Error Message', $info['last_error_message'] ?? 'None'],
                        ['Max Connections', $info['max_connections'] ?? 'Default (40)'],
                        ['Allowed Updates', empty($info['allowed_updates']) ? 'All' : implode(', ', $info['allowed_updates'])],
                    ]
                );

                if (isset($info['last_error_message'])) {
                    $this->newLine();
                    $this->warn('⚠️  There was an error with the webhook. Check the error message above.');
                }
            }

            return self::SUCCESS;
        }

        $this->error('❌ Failed to get webhook information');
        $errorDescription = $response->json('description', 'Unknown error');
        $this->error('Error: '.$errorDescription);

        return self::FAILURE;
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DeleteWebhook extends Command
{
    protected $signature = 'telegram:delete-webhook {--drop-pending : Drop all pending updates}';

    protected $description = 'Delete Telegram webhook (useful when switching to polling mode)';

    public function handle(): int
    {
        $token = config('services.telegram-bot-api.token');

        if (! $token) {
            $this->error('❌ Telegram bot token is not configured!');
            $this->info('Please set TELEGRAM_BOT_TOKEN in your .env file');

            return self::FAILURE;
        }

        $this->info('🔄 Deleting Telegram webhook...');

        $params = [];
        if ($this->option('drop-pending')) {
            $params['drop_pending_updates'] = true;
            $this->warn('⚠️  All pending updates will be dropped');
        }

        // Call Telegram API to delete webhook
        $response = Http::post(sprintf('https://api.telegram.org/bot%s/deleteWebhook', $token), $params);

        if ($response->successful() && $response->json('ok')) {
            $this->info('✅ Webhook successfully deleted!');
            $this->newLine();
            $this->info('You can now use polling mode with: php artisan telegram:polling');

            return self::SUCCESS;
        }

        $this->error('❌ Failed to delete webhook');
        $errorDescription = $response->json('description', 'Unknown error');
        $this->error('Error: '.$errorDescription);

        return self::FAILURE;
    }
}

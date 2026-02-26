<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the Telegram webhook for the bot';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $url = config('services.ngrok.url').'/api/telegram/webhook';

        $response = Http::post('https://api.telegram.org/bot'.config('services.telegram-bot-api.token').'/setWebhook', [
            'url' => $url,
        ]);

        $data = $response->json();

        if ($data['ok'] ?? false) {
            $this->info('Webhook set successfully: '.($data['description'] ?? 'OK'));
        } else {
            $this->error('Failed to set webhook: '.($data['description'] ?? 'Unknown error'));
        }
    }
}

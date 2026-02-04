<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use Illuminate\Support\Facades\Http;

readonly class SendTelegramMessage
{
    private string $token;

    public function __construct()
    {
        $this->token = config('services.telegram-bot-api.token');
    }

    public function execute(int $chatId, string $text): void
    {
        Http::post(sprintf('https://api.telegram.org/bot%s/sendMessage', $this->token), [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
}

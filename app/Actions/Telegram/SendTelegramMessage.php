<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Exceptions\TelegramMessageException;
use Illuminate\Support\Facades\Http;

readonly class SendTelegramMessage
{
    private string $token;

    public function __construct()
    {
        $this->token = config('services.telegram-bot-api.token');
    }

    /**
     * @param  array<string, mixed>|null  $replyMarkup
     *
     * @throws TelegramMessageException
     */
    public function execute(int $chatId, string $text, ?array $replyMarkup = null): void
    {
        $payload = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        $response = Http::post(
            sprintf('https://api.telegram.org/bot%s/sendMessage', $this->token),
            $payload
        );

        if ($response->failed()) {
            throw TelegramMessageException::fromResponse($chatId, $response);
        }
    }
}

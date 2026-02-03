<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MoodScore;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    /**
     * Get the bot's username from Telegram API
     */
    public function getBotUsername(): string
    {
        $token = config('services.telegram-bot-api.token');

        $response = Http::get(sprintf('https://api.telegram.org/bot%s/getMe', $token));

        if ($response->successful()) {
            return $response->json('result.username');
        }

        return 'your_bot';
    }

    public function isValidNumber($num): bool
    {
        return is_numeric($num)
            && $num >= MoodScore::VERY_BAD->value
            && $num <= MoodScore::VERY_GOOD->value;
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Support\Str;

class GenerateLinkCodeAction
{
    public function __construct(
        private TelegramService $telegramService,
    ) {}

    /**
     * @return array<int, string>
     */
    public function execute(User $user): array
    {
        $linkCode = Str::random(32);

        cache()->put('telegram_link:'.$linkCode, $user->id, now()->addMinutes(10));

        $botUsername = $this->telegramService->getBotUsername();

        $deepLink = sprintf('https://t.me/%s?start=%s', $botUsername, $linkCode);

        return [$linkCode, $deepLink, $botUsername];
    }
}

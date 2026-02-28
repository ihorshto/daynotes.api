<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use App\Services\TelegramService;
use Lorisleiva\Actions\Concerns\AsController;

class WebhookAction
{
    use AsController;

    public function __construct(
        private readonly CommandRouter $commandRouter,
        private readonly TelegramService $telegramService,
    ) {}

    /**
     * @param  array<string, mixed>  $update
     */
    public function handle(array $update): void
    {
        $message = $update['message'] ?? null;

        if (! $message) {
            return;
        }

        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'] ?? null;

        if (! $chatId) {
            return;
        }

        $user = User::query()->where('telegram_chat_id', (string) $chatId)->first();

        if ($text && str_starts_with($text, '/')) {
            $this->commandRouter->dispatch($text, (string) $chatId, $user, $update);

            return;
        }

        if ($user !== null) {
            $this->telegramService->handleState($user, $text);
        }
    }
}

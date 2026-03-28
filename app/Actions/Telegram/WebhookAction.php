<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Lorisleiva\Actions\Concerns\AsController;

class WebhookAction
{
    use AsController;

    public function __construct(
        private readonly CommandRouter $commandRouter,
        private readonly CallbackRouter $callbackRouter,
        private readonly TelegramService $telegramService,
    ) {}

    /**
     * @param  array<string, mixed>  $update
     */
    public function handle(array $update): void
    {
        if ($message = $update['message'] ?? null) {
            if ($this->isStaleMessage($message)) {
                return;
            }

            $this->handleMessage($message, $update);

            return;
        }

        if ($callbackQuery = $update['callback_query'] ?? null) {
            $this->handleCallbackQuery($callbackQuery, $update);
        }
    }

    /** @param array<string, mixed> $message */
    private function isStaleMessage(array $message): bool
    {
        $messageDate = $message['date'] ?? null;

        if (! $messageDate) {
            return false;
        }

        return (Date::now()->getTimestamp() - $messageDate) > config('services.telegram-bot-api.stale_message_threshold');
    }

    /**
     * @param  array<string, mixed>  $message
     * @param  array<string, mixed>  $update
     */
    private function handleMessage(array $message, array $update): void
    {
        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'] ?? null;

        if (! $chatId) {
            return;
        }

        $user = User::query()->where('telegram_chat_id', (string) $chatId)->first();

        if ($user instanceof User) {
            App::setLocale($user->lang->value);
        }

        if ($text && str_starts_with($text, '/')) {
            $this->commandRouter->dispatch($text, (string) $chatId, $user, $update);

            return;
        }

        if ($user !== null) {
            $this->telegramService->handleState($user, $text);
        }
    }

    /**
     * @param  array<string, mixed>  $update
     * @param  array<string, mixed>  $callbackQuery
     */
    private function handleCallbackQuery(array $callbackQuery, array $update): void
    {
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $callbackData = $callbackQuery['data'] ?? null;

        if (! $chatId || ! $callbackData) {
            return;
        }

        $user = User::query()->where('telegram_chat_id', (string) $chatId)->first();

        if ($user instanceof User) {
            App::setLocale($user->lang->value);
        }

        $this->callbackRouter->dispatch($callbackData, (string) $chatId, $user, $update);
    }
}

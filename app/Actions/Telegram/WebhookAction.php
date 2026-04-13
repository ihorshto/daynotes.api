<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
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
     * @param  array<string, mixed>  $payload
     */
    public function handle(array $payload): void
    {
        if ($message = $payload['message'] ?? null) {
            if ($this->isStaleMessage($message['date'] ?? null)) {
                return;
            }

            $this->handleMessage($payload, $message);
        }

        if ($callbackQuery = $payload['callback_query'] ?? null) {
            if ($this->isStaleMessage($callbackQuery['message']['date'] ?? null)) {
                return;
            }

            $this->handleCallbackQuery($payload, $callbackQuery);
        }
    }

    private function isStaleMessage(?int $date): bool
    {
        if (! $date) {
            return false;
        }

        return (Date::now()->getTimestamp() - $date) > config('services.telegram-bot-api.stale_message_threshold');
    }

    /**
     * @param  array<string, mixed>  $message
     * @param  array<string, mixed>  $payload
     */
    private function handleMessage(array $payload, array $message = []): void
    {
        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'] ?? null;

        if (! $chatId) {
            Log::debug('[WebhookAction] Received message without chat ID', ['message' => $message]);

            return;
        }

        $user = User::query()->where('telegram_chat_id', (string) $chatId)->first();

        if (! $user) {
            Log::debug('[WebhookAction] Received message from unlinked chat ID', ['chat_id' => $chatId]);

            return;
        }

        App::setLocale($user->lang->value);

        if ($text && str_starts_with($text, '/')) {
            $this->commandRouter->dispatch($text, (string) $chatId, $user, $payload);

            return;
        }

        $this->telegramService->handleState($user, $text);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $callbackQuery
     */
    private function handleCallbackQuery(array $payload, array $callbackQuery = []): void
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

        $this->callbackRouter->dispatch($callbackData, (string) $chatId, $user, $payload);
    }
}

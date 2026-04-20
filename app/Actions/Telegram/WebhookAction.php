<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Lorisleiva\Actions\Concerns\AsController;

class WebhookAction
{
    use AsController;

    private const CALLBACK_RATE_LIMIT = 5;

    private const CALLBACK_RATE_LIMIT_RESET_IN = 60;

    private const FLOW_LOCK_TTL = 1800;

    public function __construct(
        private readonly CommandRouter $commandRouter,
        private readonly CallbackRouter $callbackRouter,
        private readonly TelegramService $telegramService,
        private readonly SendTelegramMessage $sendTelegramMessage,
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
        $messageId = $callbackQuery['message']['message_id'] ?? null;

        if (! $chatId || ! $callbackData) {
            return;
        }

        if ($this->isCallbackRateLimited((int) $chatId)) {
            return;
        }

        if ($this->isFlowLocked((int) $chatId, $messageId !== null ? (int) $messageId : null)) {
            return;
        }

        $user = User::query()->where('telegram_chat_id', (string) $chatId)->first();

        if ($user instanceof User) {
            App::setLocale($user->lang->value);
        }

        $this->callbackRouter->dispatch($callbackData, (string) $chatId, $user, $payload);
    }

    private function isFlowLocked(int $chatId, ?int $messageId): bool
    {
        if (! $messageId) {
            return false;
        }

        $lockKey = 'callback-lock:'.$chatId.':'.$messageId;

        if (Cache::has($lockKey)) {
            return true;
        }

        Cache::put($lockKey, true, self::FLOW_LOCK_TTL);

        return false;
    }

    private function isCallbackRateLimited(int $chatId): bool
    {
        $key = 'rate-limit:'.$chatId;

        if (RateLimiter::tooManyAttempts($key, self::CALLBACK_RATE_LIMIT)) {
            $notificationKey = 'notification-rate-limit:'.$chatId;
            if (! RateLimiter::tooManyAttempts($notificationKey, 1)) {
                RateLimiter::hit($notificationKey, self::CALLBACK_RATE_LIMIT_RESET_IN);
                $this->sendTelegramMessage->execute($chatId, __('messages.rate_limit.webhook_exceeded'));
            }

            Log::info('[WebhookAction] Rate limit check - too many attempts for chat ID', ['chat_id' => $chatId]);

            return true;
        }

        RateLimiter::hit($key, self::CALLBACK_RATE_LIMIT_RESET_IN);

        return false;
    }
}

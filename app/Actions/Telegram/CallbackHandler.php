<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use App\Services\StateManagerService;

abstract class CallbackHandler
{
    public function __construct(
        protected readonly string $chatId,
        protected readonly ?User $user,
        protected readonly array $update,
        protected readonly SendTelegramMessage $sendTelegramMessage,
        protected readonly AnswerCallbackQuery $answerCallbackQuery,
        protected readonly StateManagerService $stateManager,
    ) {}

    abstract public static function accepts(string $callbackData): bool;

    abstract public function handle(): void;

    protected function reply(string $text): void
    {
        $this->sendTelegramMessage->execute((int) $this->chatId, $text);
    }

    /**
     * @param  array<int, array<int, array<string, string>>>  $keyboard
     */
    protected function replyWithKeyboard(string $text, array $keyboard): void
    {
        $this->sendTelegramMessage->execute(
            (int) $this->chatId,
            $text,
            ['inline_keyboard' => $keyboard]
        );
    }

    protected function acknowledge(string $toast = ''): void
    {
        $callbackQueryId = $this->update['callback_query']['id'] ?? '';

        $this->answerCallbackQuery->execute($callbackQueryId, $toast);
    }
}

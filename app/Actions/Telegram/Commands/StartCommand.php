<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\Command;
use App\Models\User;

class StartCommand extends Command
{
    public static function getName(): string
    {
        return '/start';
    }

    public function handle(): void
    {
        $text = $this->update['message']['text'] ?? '';

        if (str_starts_with($text, '/start ')) {
            $this->handleDeepLink($text);

            return;
        }

        $this->reply(__('messages.start.greeting'));
    }

    private function handleDeepLink(string $text): void
    {
        $linkCode = mb_substr($text, 7);
        $userId = cache()->pull('telegram_link:'.$linkCode);

        if (! $userId) {
            $this->reply(__('messages.start.invalid_code'));

            return;
        }

        $user = User::query()->find($userId);

        if (! $user) {
            $this->reply(__('messages.start.user_not_found'));

            return;
        }

        if ($user->telegram_chat_id) {
            $this->reply(__('messages.start.already_linked'));

            return;
        }

        $user->telegram_chat_id = $this->chatId;
        $user->save();

        $username = $this->update['message']['from']['username'] ?? 'User';

        $this->reply(__('messages.start.success', ['username' => $username]));
    }
}

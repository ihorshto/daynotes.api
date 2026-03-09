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

        $this->reply(
            "👋 *Привіт!*\n\n"
            ."Це Mood Tracker Bot.\n\n"
            ."Для підключення:\n"
            ."1. Відкрийте Mood Tracker\n"
            ."2. Перейдіть в Налаштування\n"
            ."3. Натисніть 'Підключити Telegram'\n"
            .'4. Відкрийте посилання'
        );
    }

    private function handleDeepLink(string $text): void
    {
        $linkCode = mb_substr($text, 7);
        $userId = cache()->pull('telegram_link:'.$linkCode);

        if (! $userId) {
            $this->reply(
                "❌ *Код застарів або невалідний*\n\n"
                .'Будь ласка, згенеруйте новий код у налаштуваннях Mood Tracker.'
            );

            return;
        }

        $user = User::query()->find($userId);

        if (! $user) {
            $this->reply('❌ Користувача не знайдено.');

            return;
        }

        if ($user->telegram_chat_id) {
            $this->reply(
                "⚠️ *Вже підключено*\n\n"
                .'Ваш акаунт вже підключено до іншого Telegram чату. '
                .'Спочатку відключіть його через налаштування.'
            );

            return;
        }

        $user->telegram_chat_id = $this->chatId;
        $user->save();

        $username = $this->update['message']['from']['username'] ?? 'User';

        $this->reply(
            "✅ *Вітаємо, {$username}!*\n\n"
            ."Telegram успішно підключено до Mood Tracker! 😊\n\n"
            .'Тепер ви можете записувати настрій та отримувати нагадування. 🎉'
        );
    }
}

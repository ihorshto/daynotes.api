<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\Command;
use App\Models\User;

class UnlinkCommand extends Command
{
    public static function getName(): string
    {
        return '/unlink';
    }

    public function handle(): void
    {
        if (! $this->user instanceof User || ! $this->user->telegram_chat_id) {
            $this->reply(
                "❌ *Не підключено*\n\n"
                .'Ваш акаунт не підключений до жодного Telegram чату.'
            );

            return;
        }

        $this->user->telegram_chat_id = null;
        $this->user->save();

        $this->reply(
            "✅ *Відключено успішно*\n\n"
            .'Ваш акаунт відключено від цього Telegram чату.'
        );
    }
}

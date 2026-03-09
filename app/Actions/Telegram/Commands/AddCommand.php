<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\Command;
use App\Models\User;

class AddCommand extends Command
{
    public static function getName(): string
    {
        return '/add';
    }

    public function handle(): void
    {
        if (! $this->user instanceof User) {
            $this->reply('❌ Акаунт не підключено до Mood Tracker. Використайте /start для підключення.');

            return;
        }

        $this->replyWithKeyboard(
            '🎭 *Як ти себе почуваєш?*',
            [[
                ['text' => '1 😢', 'callback_data' => 'mood:1'],
                ['text' => '2 😞', 'callback_data' => 'mood:2'],
                ['text' => '3 😐', 'callback_data' => 'mood:3'],
                ['text' => '4 😊', 'callback_data' => 'mood:4'],
                ['text' => '5 🎉', 'callback_data' => 'mood:5'],
            ]]
        );
    }
}

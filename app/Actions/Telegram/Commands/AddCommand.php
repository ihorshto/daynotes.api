<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\Command;
use App\Actions\Telegram\SendTelegramMessage;
use App\Enums\UserState;
use App\Models\User;
use App\Services\StateManagerService;

class AddCommand extends Command
{
    public function __construct(
        string $chatId,
        ?User $user,
        array $update,
        SendTelegramMessage $sendTelegramMessage,
        private readonly StateManagerService $stateManager,
    ) {
        parent::__construct($chatId, $user, $update, $sendTelegramMessage);
    }

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

        $this->stateManager->set($this->user, UserState::WaitingForMood);

        $this->reply('Обери настрій від 1 до 5 (1 - дуже погано, 5 - відмінно) 🎭');
    }
}

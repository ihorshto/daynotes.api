<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Callbacks;

use App\Actions\Telegram\CallbackHandler;
use App\Enums\UserState;
use App\Models\User;

class MoodSelectionCallbackHandler extends CallbackHandler
{
    public static function accepts(string $callbackData): bool
    {
        return str_starts_with($callbackData, 'mood:');
    }

    public function handle(): void
    {
        if (! $this->user instanceof User) {
            $this->acknowledge();
            $this->reply('❌ Акаунт не підключено до Mood Tracker. Використайте /start для підключення.');

            return;
        }

        $callbackData = $this->update['callback_query']['data'] ?? '';
        $moodScore = (int) str_replace('mood:', '', $callbackData);

        $this->stateManager->set($this->user, UserState::WaitingForNote, ['mood_score' => $moodScore]);

        $this->acknowledge();

        $this->replyWithKeyboard(
            "✅ Настрій *{$moodScore}* зафіксовано!\n\n📝 Додай нотатку або пропусти:",
            [[
                ['text' => '⏭️ Пропустити', 'callback_data' => 'skip_note'],
            ]]
        );
    }
}

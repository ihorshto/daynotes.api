<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Callbacks;

use App\Actions\Telegram\CallbackHandler;
use App\Models\MoodEntry;
use App\Models\User;

class SkipNoteCallbackHandler extends CallbackHandler
{
    public static function accepts(string $callbackData): bool
    {
        return $callbackData === 'skip_note';
    }

    public function handle(): void
    {
        if (! $this->user instanceof User) {
            $this->acknowledge();
            $this->reply(__('messages.common.not_linked'));

            return;
        }

        $payload = $this->stateManager->getPayload($this->user);

        MoodEntry::query()->create([
            'user_id'    => $this->user->id,
            'mood_score' => $payload['mood_score'],
            'note'       => null,
        ]);

        $this->stateManager->clear($this->user);

        $this->acknowledge(__('messages.mood.saved_ack'));

        $this->reply(__('messages.mood.saved_no_note'));
    }
}

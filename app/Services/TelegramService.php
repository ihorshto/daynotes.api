<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Telegram\SendTelegramMessage;
use App\Enums\UserState;
use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function __construct(
        private readonly SendTelegramMessage $sendTelegramMessage,
        private readonly StateManagerService $stateManager,
    ) {}

    /**
     * Get the bot's username from Telegram API
     */
    public function getBotUsername(): string
    {
        $token = config('services.telegram-bot-api.token');

        $response = Http::get(sprintf('https://api.telegram.org/bot%s/getMe', $token));

        if ($response->successful()) {
            return $response->json('result.username');
        }

        return 'your_bot';
    }

    public function handleState(User $user, ?string $text): void
    {
        $state = $this->stateManager->get($user);
        $chatId = (int) $user->telegram_chat_id;

        switch ($state) {

            case UserState::WaitingForNote:

                $payload = $this->stateManager->getPayload($user);

                MoodEntry::query()->create([
                    'user_id'    => $user->id,
                    'mood_score' => $payload['mood_score'],
                    'note'       => $text,
                ]);

                $this->stateManager->clear($user);

                $this->sendTelegramMessage->execute($chatId, __('messages.mood.saved'));

                break;

            default:
                break;
        }
    }
}

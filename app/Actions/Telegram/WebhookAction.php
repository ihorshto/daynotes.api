<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Actions\MoodEntry\CreateMoodEntryAction;
use App\Models\User;
use App\Services\TelegramCommandsService;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsController;

class WebhookAction
{
    use AsController;

    public function __construct(
        private readonly CreateMoodEntryAction $createMoodEntryAction,
        private readonly TelegramService $telegramService,
        private readonly sendTelegramMessage $sendTelegramMessage,
        private readonly TelegramCommandsService $telegramCommandsService,
    ) {}

    public function execute(Request $request): JsonResponse
    {
        $update = $request->all();

        if (! isset($update['message'])) {
            return response()->json(['ok' => true]);
        }

        $message = $update['message'];
        $text = trim($message['text'] ?? '');
        $chatId = $message['chat']['id'];

        if (str_starts_with($text, '/')) {
            return $this->telegramCommandsService->getCommandResponse($message, $text);
        }
        // Find User
        $user = User::query()->where('telegram_chat_id', $chatId)->firstOrFail();
        if (! $user) {
            $this->sendTelegramMessage->execute(
                $chatId,
                '❌ Please link your Telegram account first using /start command.'
            );

            return response()->json(['ok' => true]);
        }
        // Handle state
        $this->handleUserState($user, $text, $chatId);

        return response()->json(['ok' => true]);
    }

    private function handleUserState(User $user, string $text, int $chatId): void
    {
        $cacheKey = 'telegram_awaiting_description:'.$chatId;
        $pendingMoodEntryId = cache()->get($cacheKey);

        if ($this->telegramService->isValidNumber($text)) {
            $moodEntry = $this->createMoodEntryAction->execute(
                $user,
                (int) $text,
                null
            );

            cache()->put($cacheKey, $moodEntry->id, now()->addMinutes(30));

            $this->sendTelegramMessage->execute(
                $chatId,
                "✅ Your mood has been saved!\n\n"
                .'Would you like to add a note? Just type it now, or send another number to log a new mood.'
            );

            return;
        }

        if ($pendingMoodEntryId) {
            $moodEntry = $user->moodEntries()->find($pendingMoodEntryId);

            if ($moodEntry) {
                $moodEntry->update(['note' => $text]);
                cache()->forget($cacheKey);

                $this->sendTelegramMessage->execute(
                    $chatId,
                    '📝 Note added to your mood entry!'
                );

                return;
            }
        }

        cache()->forget($cacheKey);

        $this->sendTelegramMessage->execute(
            $chatId,
            'Please enter a number from *1 to 5* to log your mood.'
        );
    }
}

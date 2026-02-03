<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Actions\MoodEntry\CreateMoodEntryAction;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsController;

class WebhookAction
{
    use AsController;

    public function __construct(
        private readonly CreateMoodEntryAction $createMoodEntryAction,
        private readonly TelegramService $telegramService,
    ) {}

    public function execute(Request $request)
    {
        $update = $request->all();

        if (! isset($update['message'])) {
            return response()->json(['ok' => true]);
        }

        $message = $update['message'];
        $text = trim($message['text'] ?? '');
        $chatId = $message['chat']['id'];

        // /start
        if (str_starts_with($text, '/start')) {
            $this->handleStartCommande($update['message']);

            return response()->json(['ok' => true]);
        }

        if (str_starts_with($text, '/unlink')) {
            $this->handleUnLinkCommande($update['message']);

            return response()->json(['ok' => true]);
        }

        // Find User
        $user = User::query()->where('telegram_chat_id', $chatId)->firstOrFail();

        if (! $user) {
            $this->sendTelegramMessage(
                $chatId,
                '❌ Please link your Telegram account first using /start command.'
            );

            return response()->json(['ok' => true]);
        }

        // Handle state
        $this->handleUserState($user, $text, $chatId);

        return response()->json(['ok' => true]);
    }

    /**
     * @param  array<string, mixed>  $message
     */
    public function handleUnLinkCommande(array $message): JsonResponse
    {
        $chatId = $message['chat']['id'];
        $user = User::query()->where('telegram_chat_id', $chatId)->firstOrFail();

        if (! $user) {
            return response()->json(['ok' => true]);
        }

        if ($user->telegram_chat_id) {

            $user->telegram_chat_id = null;
            $user->save();

            $this->sendTelegramMessage(
                $chatId,
                "️✅ *Unlinked Successfully*\n\n"
                ."Your Mood Tracker account has been unlinked from this Telegram chat. \n\n");

            return response()->json(['ok' => true, 'status' => 'already_linked']);
        }

        $this->sendTelegramMessage(
            $chatId,
            "❌ *Not Linked*\n\n"
            ."Your Mood Tracker account is not linked to any Telegram chat. \n\n"
            .'If you want to link it, please use the /start command in your Mood Tracker app settings.'
        );

        return response()->json(['ok' => true, 'status' => 'not_linked']);

    }

    /**
     * Send a message via Telegram Bot API
     */
    private function sendTelegramMessage(int $chatId, string $text): void
    {
        $token = config('services.telegram-bot-api.token');

        Http::post(sprintf('https://api.telegram.org/bot%s/sendMessage', $token), [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

    /**
     * @param  array<string, mixed>  $message
     */
    private function handleStartCommande(array $message)
    {
        $text = $message['text'];
        if (str_starts_with($text, '/start ')) {
            $linkCode = mb_substr($text, 7);
            Log::info('link code received: '.$linkCode);
            Log::info('username : '.($message['from']['username'] ?? 'unknown'));
            $chatId = $message['chat']['id'];
            $username = $message['from']['username'] ?? 'User';
            $userId = cache()->pull('telegram_link:'.$linkCode);

            if ($userId) {
                $user = User::query()->find($userId);
                if ($user) {
                    if ($user->telegram_chat_id) {
                        $this->sendTelegramMessage(
                            $chatId,
                            "⚠️ *Already Linked*\n\n"
                            ."Your Mood Tracker account is already linked to another Telegram chat. \n\n"
                            .'If you want to link it to this chat, please unlink it from the previous chat first in your Mood Tracker app settings.'
                        );

                        return response()->json(['ok' => true, 'status' => 'already_linked']);
                    }

                    $user->telegram_chat_id = $chatId;
                    $user->save();

                    $this->sendTelegramMessage(
                        $chatId,
                        "✅ *Congratulations, {$username}!*\n\n"
                        ."Telegram notifications have been successfully linked to your account Mood Tracker! \n\n"
                        ."Now you'll receive mood reminders and other notifications directly here. 😊 \n\n"
                        .'Welcome aboard! 🎉'
                    );

                    return response()->json(['ok' => true, 'status' => 'linked']);

                }
            } else {
                $this->sendTelegramMessage(
                    $chatId,
                    "❌ *Code Expired or Invalid*\n\n"
                    ."The link code is valid for 10 minutes only. \n\n"
                    .'Please generate a new link code from your Mood Tracker app settings and try again.'
                );

                return response()->json(['ok' => true, 'status' => 'expired']);
            }
        } elseif ($text === '/start') {
            $chatId = $message['chat']['id'];

            $this->sendTelegramMessage(
                $chatId,
                "👋 *Hi!*\n\n"
                ."It's a Mood Tracker Bot.\n\n"
                ."To link this bot to your Mood Tracker account: \n"
                ."1. Open your Mood Tracker app. \n"
                ."2. Go to Settings \n"
                ."3. Click 'Connect Telegram' \n"
                .'4. Open the provided link'
            );
        }
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

            $this->sendTelegramMessage(
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

                $this->sendTelegramMessage(
                    $chatId,
                    '📝 Note added to your mood entry!'
                );

                return;
            }
        }

        cache()->forget($cacheKey);

        $this->sendTelegramMessage(
            $chatId,
            'Please enter a number from *1 to 5* to log your mood.'
        );
    }
}

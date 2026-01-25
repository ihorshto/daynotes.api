<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Actions\MoodEntry\CreateMoodEntryAction;
use App\Enums\MoodScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsController;

class WebhookAction
{
    use AsController;

    public function __construct(private readonly CreateMoodEntryAction $createMoodEntryAction) {}

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

        // Find User
        $user = User::query()->whereHas('notificationSetting', function ($query) use ($chatId): void {
            $query->where('telegram_chat_id', $chatId);
        })->first();

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
            $chatId = $message['chat']['id'];
            $username = $message['from']['username'] ?? 'User';
            $userId = cache()->pull('telegram_link:'.$linkCode);

            if ($userId) {
                $user = User::query()->find($userId);
                if ($user) {
                    $user->notificationSetting()->updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'telegram_chat_id' => $chatId,
                            'telegram_enabled' => true,
                        ]
                    );

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
                ."2. Go to Settings > Notifications \n"
                ."3. Click 'Connect Telegram' \n"
                .'4. Open the provided link'
            );
        }
    }

    private function handleUserState(User $user, string $text, int $chatId): void
    {
        if (is_numeric($text) && $text >= MoodScore::VERY_BAD->value && $text <= MoodScore::VERY_GOOD->value) {
            $this->createMoodEntryAction->execute(
                $user,
                (int) $text,
                null
            );

            $this->sendTelegramMessage(
                $chatId,
                'Thank you! Your mood has been saved.'
            );
        } else {
            $this->sendTelegramMessage(
                $chatId,
                'Please enter a number from *1 to 5*'
            );
        }
    }
}

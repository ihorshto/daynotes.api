<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsController;

class WebhookAction
{
    use AsController;

    public function execute(Request $request)
    {
        $update = $request->all();

        // Handle /start command with link code
        if (isset($update['message']['text'])) {
            $text = $update['message']['text'];

            if (str_starts_with($text, '/start ')) {
                $linkCode = mb_substr($text, 7);
                $chatId = $update['message']['chat']['id'];
                $username = $update['message']['from']['username'] ?? 'User';
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
                $chatId = $update['message']['chat']['id'];

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

        return response()->json(['ok' => true]);
    }

    /**
     * Send a message via Telegram Bot API
     */
    private function sendTelegramMessage(string $chatId, string $text): void
    {
        Log::info('chatId '.$chatId);
        Log::info('text '.$text);
        $token = config('services.telegram-bot-api.token');

        Http::post(sprintf('https://api.telegram.org/bot%s/sendMessage', $token), [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
}

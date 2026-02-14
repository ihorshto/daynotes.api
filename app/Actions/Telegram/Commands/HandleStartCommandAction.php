<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\SendTelegramMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

readonly class HandleStartCommandAction
{
    public function __construct(
        private SendTelegramMessage $sendTelegramMessage,
    ) {}

    /**
     * @param  array<string, mixed>  $message
     */
    public function handle(array $message): JsonResponse
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
                        $this->sendTelegramMessage->execute(
                            $chatId,
                            "⚠️ *Already Linked*\n\n"
                            ."Your Mood Tracker account is already linked to another Telegram chat. \n\n"
                            .'If you want to link it to this chat, please unlink it from the previous chat first in your Mood Tracker app settings.'
                        );

                        return response()->json(['ok' => true, 'status' => 'already_linked']);
                    }

                    $user->telegram_chat_id = $chatId;
                    $user->save();

                    $this->sendTelegramMessage->execute(
                        $chatId,
                        "✅ *Congratulations, {$username}!*\n\n"
                        ."Telegram notifications have been successfully linked to your account Mood Tracker! \n\n"
                        ."Now you'll receive mood reminders and other notifications directly here. 😊 \n\n"
                        .'Welcome aboard! 🎉'
                    );

                    return response()->json(['ok' => true, 'status' => 'linked']);

                }
            } else {
                $this->sendTelegramMessage->execute(
                    $chatId,
                    "❌ *Code Expired or Invalid*\n\n"
                    ."The link code is valid for 10 minutes only. \n\n"
                    .'Please generate a new link code from your Mood Tracker app settings and try again.'
                );

                return response()->json(['ok' => true, 'status' => 'expired']);
            }
        } elseif ($text === '/start') {
            $chatId = $message['chat']['id'];

            $this->sendTelegramMessage->execute(
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
}

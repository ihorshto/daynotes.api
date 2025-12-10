<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    /**
     * Generate a Telegram deep link with a unique code
     */
    public function generateLinkCode(Request $request)
    {
        $user = $request->user();

        $linkCode = Str::random(32);

        cache()->put("telegram_link:{$linkCode}", $user->id, now()->addMinutes(10));

        $botUsername = $this->getBotUsername();

        $deepLink = "https://t.me/{$botUsername}?start={$linkCode}";

        return response()->json([
            'link_code' => $linkCode,
            'deep_link' => $deepLink,
            'bot_username' => $botUsername,
            'expires_at' => now()->addMinutes(10)->toIso8601String(),
            'instructions' => [
                '1. Click the link or open Telegram and search for @'.$botUsername,
                '2. Press "Start" to initiate the bot.',
                '3. The bot will automatically link to your account.',
            ],
        ]);
    }

    /**
     * Handle incoming Telegram webhook
     */
    public function webhook(Request $request)
    {
        $update = $request->all();

        // Handle /start command with link code
        if (isset($update['message']['text'])) {
            $text = $update['message']['text'];

            if (str_starts_with($text, '/start ')) {
                $linkCode = substr($text, 7);
                $chatId = $update['message']['chat']['id'];
                $username = $update['message']['from']['username'] ?? 'User';
                $userId = cache()->pull("telegram_link:{$linkCode}");

                if ($userId) {
                    $user = User::find($userId);

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
                            "✅ *Congratulations, {$username}!*\n\n".
                            "Telegram notifications have been successfully linked to your account Mood Tracker! \n\n".
                            "Now you'll receive mood reminders and other notifications directly here. 😊 \n\n".
                            'Welcome aboard! 🎉'
                        );

                        return response()->json(['ok' => true, 'status' => 'linked']);
                    }
                } else {
                    $this->sendTelegramMessage(
                        $chatId,
                        "❌ *Code Expired or Invalid*\n\n".
                        "The link code is valid for 10 minutes only. \n\n".
                        'Please generate a new link code from your Mood Tracker app settings and try again.'
                    );

                    return response()->json(['ok' => true, 'status' => 'expired']);
                }
            } elseif ($text === '/start') {
                $chatId = $update['message']['chat']['id'];

                $this->sendTelegramMessage(
                    $chatId,
                    "👋 *Hi!*\n\n".
                    "It's a Mood Tracker Bot.\n\n".
                    "To link this bot to your Mood Tracker account: \n".
                    "1. Open your Mood Tracker app. \n".
                    "2. Go to Settings > Notifications \n".
                    "3. Click 'Connect Telegram' \n".
                    '4. Open the provided link'
                );
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Disconnect Telegram notifications
     */
    public function disconnect(Request $request)
    {
        $user = $request->user();
        $settings = $user->notificationSetting;

        if ($settings && $settings->telegram_chat_id) {
            // Send a disconnection message
            try {
                $this->sendTelegramMessage(
                    $settings->telegram_chat_id,
                    " *Telegram Notifications Disconnected*\n\n".
                    "Your Telegram no longer will receive notifications from our app. \n".
                    'If this was a mistake, you can reconnect anytime from your account settings.'
                );
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram disconnection message: '.$e->getMessage());
            }
        }

        $user->notificationSetting()->update([
            'telegram_chat_id' => null,
            'telegram_enabled' => false,
        ]);

        return response()->json(['message' => 'Telegram успішно відключено']);
    }

    /**
     * Check Telegram notification status
     */
    public function status(Request $request)
    {
        $user = $request->user();
        $settings = $user->notificationSetting;

        return response()->json([
            'connected' => $settings && $settings->telegram_enabled && $settings->telegram_chat_id,
            'enabled' => $settings ? $settings->telegram_enabled : false,
        ]);
    }

    /**
     * Send a message via Telegram Bot API
     */
    private function sendTelegramMessage(string $chatId, string $text)
    {
        $token = config('services.telegram-bot-api.token');

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

    /**
     * Get the bot's username from Telegram API
     */
    private function getBotUsername(): string
    {
        $token = config('services.telegram-bot-api.token');

        Log::info('token : '.$token);

        $response = Http::get("https://api.telegram.org/bot{$token}/getMe");

        if ($response->successful()) {
            return $response->json('result.username');
        }

        return 'your_bot';
    }
}

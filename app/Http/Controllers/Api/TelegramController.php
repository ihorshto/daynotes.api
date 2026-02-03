<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Telegram\GenerateLinkCodeAction;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    /**
     * Generate a Telegram deep link with a unique code
     */
    public function generateLinkCode(Request $request, GenerateLinkCodeAction $generateLinkCodeAction): JsonResponse
    {
        [$linkCode, $deepLink, $botUsername] = $generateLinkCodeAction->execute($request->user());

        return response()->json([
            'link_code'    => $linkCode,
            'deep_link'    => $deepLink,
            'bot_username' => $botUsername,
            'expires_at'   => now()->addMinutes(10)->toIso8601String(),
            'instructions' => [
                '1. Click the link or open Telegram and search for @'.$botUsername,
                '2. Press "Start" to initiate the bot.',
                '3. The bot will automatically link to your account.',
            ],
        ]);
    }

    /**
     * Disconnect Telegram notifications
     */
    public function disconnect(Request $request)
    {
        $user = $request->user();
        $previousTelegramId = $user->telegram_chat_id;

        if ($previousTelegramId) {
            $user->telegram_chat_id = null;
            $user->save();

            // Send a disconnection message
            try {
                $this->sendTelegramMessage(
                    $previousTelegramId,
                    " *Telegram Notifications Disconnected*\n\n"
                    ."Your Telegram no longer will receive notifications from our app. \n"
                    .'If this was a mistake, you can reconnect anytime from your account settings.'
                );
            } catch (Exception $e) {
                Log::error('Failed to send Telegram disconnection message: '.$e->getMessage());
            }

            return response()->json([
                'ok'      => true,
                'message' => 'Telegram disconnected successfully',
            ]);
        }

        return response()->json([
            'ok'      => false,
            'message' => 'Telegram is not connected.',
        ]);

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
            'enabled'   => $settings ? $settings->telegram_enabled : false,
        ]);
    }

    /**
     * Send a message via Telegram Bot API
     */
    private function sendTelegramMessage(string $chatId, string $text): void
    {
        $token = config('services.telegram-bot-api.token');

        Http::post(sprintf('https://api.telegram.org/bot%s/sendMessage', $token), [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
}

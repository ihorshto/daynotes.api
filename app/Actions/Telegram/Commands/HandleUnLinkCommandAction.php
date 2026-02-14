<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\SendTelegramMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;

readonly class HandleUnLinkCommandAction
{
    public function __construct(
        private SendTelegramMessage $sendTelegramMessage,
    ) {}

    /**
     * @param  array<string, mixed>  $message
     */
    public function handle(array $message): JsonResponse
    {
        $chatId = $message['chat']['id'];
        $user = User::query()->where('telegram_chat_id', $chatId)->firstOrFail();

        if (! $user) {
            return response()->json(['ok' => true]);
        }

        if ($user->telegram_chat_id) {

            $user->telegram_chat_id = null;
            $user->save();

            $this->sendTelegramMessage->execute(
                $chatId,
                "️✅ *Unlinked Successfully*\n\n"
                ."Your Mood Tracker account has been unlinked from this Telegram chat. \n\n");

            return response()->json(['ok' => true, 'status' => 'already_linked']);
        }

        $this->sendTelegramMessage->execute(
            $chatId,
            "❌ *Not Linked*\n\n"
            ."Your Mood Tracker account is not linked to any Telegram chat. \n\n"
            .'If you want to link it, please use the /start command in your Mood Tracker app settings.'
        );

        return response()->json(['ok' => true, 'status' => 'not_linked']);
    }
}

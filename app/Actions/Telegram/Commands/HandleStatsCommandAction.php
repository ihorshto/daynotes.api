<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\MoodEntry\GetMoodStatisticAction;
use App\Actions\Telegram\SendTelegramMessage;
use App\Enums\StatsPeriod;
use App\Models\User;
use Illuminate\Http\JsonResponse;

readonly class HandleStatsCommandAction
{
    public function __construct(
        private SendTelegramMessage $sendTelegramMessage,
        private GetMoodStatisticAction $getMoodStatisticAction,
    ) {}

    /**
     * @param  array<string, mixed>  $message
     */
    public function handle(array $message): JsonResponse
    {
        $text = $message['text'];
        $chatId = $message['chat']['id'];

        $stats = StatsPeriod::fromCommand($text);

        if (! $stats instanceof StatsPeriod) {
            $this->sendTelegramMessage->execute(
                $chatId,
                "❌ Unknown command. Available commands: \n"
                ."*\stats_daily* \n"
                ."*\stats_weekly* \n"
                ."*\stats_monthly*"
            );

            return response()->json(['ok' => true, 'status' => 'unknown_stats_command']);
        }

        $user = User::query()->where('telegram_chat_id', $chatId)->first();

        if ($user === null) {
            return response()->json(['ok' => false, 'status' => 'user_not_found']);
        }

        $statistic = $this->getMoodStatisticAction->execute($user, $stats);

        if ($statistic['count'] === 0) {
            $this->sendTelegramMessage->execute(
                $chatId,
                'No mood entries found for this period :('
            );
        } else {
            $this->sendTelegramMessage->execute(
                $chatId,
                "📊 * {$stats->label()}* \n"
                ."Average Mood: *{$statistic['average']}* \n"
                ."Entries Count: *{$statistic['count']}* \n"
                ."Min Mood: *{$statistic['min']}* \n"
                ."Max Mood: *{$statistic['max']}* \n"
                .'Keep write you mood!'
            );
        }

        return response()->json(['ok' => true]);
    }
}

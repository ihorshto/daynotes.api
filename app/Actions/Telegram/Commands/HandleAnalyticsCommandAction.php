<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\SendTelegramMessage;
use App\Enums\AnalyticsPeriod;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

readonly class HandleAnalyticsCommandAction
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
        $chatId = $message['chat']['id'];

        $period = AnalyticsPeriod::fromCommand($text);

        if (! $period instanceof AnalyticsPeriod) {
            $this->sendTelegramMessage->execute(
                $chatId,
                "❌ Unknown command. Available commands:\n"
                ."*/analytics_daily*\n"
                ."*/analytics_weekly*\n"
                .'*/analytics_monthly*'
            );

            return response()->json(['ok' => true]);
        }

        $user = User::query()
            ->where('telegram_chat_id', $chatId)
            ->first();

        if ($user === null) {
            return response()->json([
                'ok'     => false,
                'status' => 'user_not_found',
            ]);
        }

        [$from, $to] = $period->dateRange();

        $this->sendTelegramMessage->execute(
            $chatId,
            sprintf('🧠 Generating %s analytics...', $period->label())
        );

        $response = Http::post(
            config('services.n8n.url').'/webhook/mood-analytics',
            [
                'user_id'   => $user->id,
                'from_date' => $from->toDateString(),
                'to_date'   => $to->toDateString(),
                'period'    => $period->value,
            ]
        );

        if (! $response->successful()) {
            $this->sendTelegramMessage->execute(
                $chatId,
                '⚠️ Analytics service unavailable.'
            );

            return response()->json(['ok' => true]);
        }

        $text = $response->json('output') ?? 'No analytics available.';

        $this->sendTelegramMessage->execute($chatId, $text);

        return response()->json(['ok' => true]);
    }
}

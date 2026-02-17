<?php

declare(strict_types=1);

use App\Actions\MoodEntry\GetMoodStatisticAction;
use App\Actions\Telegram\Commands\HandleStatsCommandAction;
use App\Enums\StatsPeriod;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

/**
 * @return array<string, string|array<string, int>>
 */
function makeTelegramMessage(string $text, int $chatId = 602882484): array
{
    return [
        'text' => $text,
        'chat' => ['id' => $chatId],
    ];
}

describe('Handle statistic command', function (): void {

    it('sends formatted statistics for valid commands', function (string $command, StatsPeriod $period): void {
        Http::fake();
        $chatId = 602882484;
        User::factory()->create(['telegram_chat_id' => $chatId]);

        $this->mock(GetMoodStatisticAction::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn(['average' => 3.5, 'count' => 5, 'min' => 1, 'max' => 5]);

        $response = resolve(HandleStatsCommandAction::class)->handle(
            makeTelegramMessage($command, $chatId)
        );

        expect($response->getData(true))->toBe(['ok' => true]);

        Http::assertSent(function (Request $request) use ($chatId, $period): bool {
            return $request['chat_id'] === $chatId
                && str_contains($request['text'], $period->label())
                && str_contains($request['text'], '3.5')
                && str_contains($request['text'], '5')
                && str_contains($request['text'], '1');
        });
    })->with([
        'daily'   => ['/stats_daily', StatsPeriod::DAILY],
        'weekly'  => ['/stats_weekly', StatsPeriod::WEEKLY],
        'monthly' => ['/stats_monthly', StatsPeriod::MONTHLY],
    ]);

    it('sends no entries message when count is zero', function (): void {
        Http::fake();
        $chatId = 602882484;
        User::factory()->create(['telegram_chat_id' => $chatId]);

        $this->mock(GetMoodStatisticAction::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn(['average' => 0, 'count' => 0, 'min' => 0, 'max' => 0]);

        $response = resolve(HandleStatsCommandAction::class)->handle(
            makeTelegramMessage('/stats_daily', $chatId)
        );

        expect($response->getData(true))->toBe(['ok' => true]);

        Http::assertSent(function (Request $request) use ($chatId): bool {
            return $request['chat_id'] === $chatId
                && $request['text'] === 'No mood entries found for this period :(';
        });
    });

    it('sends error message for unknown stats command', function (): void {
        Http::fake();
        $chatId = 602882484;

        $response = resolve(HandleStatsCommandAction::class)->handle(
            makeTelegramMessage('/stats_yearly', $chatId)
        );

        expect($response->getData(true))->toBe(['ok' => true, 'status' => 'unknown_stats_command']);

        Http::assertSent(function (Request $request) use ($chatId): bool {
            return $request['chat_id'] === $chatId
                && str_contains($request['text'], 'Unknown command');
        });
    });

    it('returns user not found when no matching user exists', function (): void {
        Http::fake();
        $chatId = 602882484;

        $response = resolve(HandleStatsCommandAction::class)->handle(
            makeTelegramMessage('/stats_daily', $chatId)
        );

        expect($response->getData(true))->toBe(['ok' => false, 'status' => 'user_not_found']);

        Http::assertNothingSent();
    });
});

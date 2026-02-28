<?php

declare(strict_types=1);

use App\Actions\MoodEntry\GetMoodStatisticAction;
use App\Actions\Telegram\Commands\StatsCommand;
use App\Enums\StatsPeriod;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

describe('StatsCommand', function (): void {

    it('sends formatted statistics for valid commands', function (string $command, StatsPeriod $period): void {
        Http::fake();
        $chatId = 602882484;
        $user = User::factory()->create(['telegram_chat_id' => $chatId]);

        $this->mock(GetMoodStatisticAction::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn(['average' => 3.5, 'count' => 5, 'min' => 1, 'max' => 5]);

        $update = ['message' => ['text' => $command, 'chat' => ['id' => $chatId]]];

        resolve(StatsCommand::class, [
            'chatId' => (string) $chatId,
            'user'   => $user,
            'update' => $update,
        ])->handle();

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
        $user = User::factory()->create(['telegram_chat_id' => $chatId]);

        $this->mock(GetMoodStatisticAction::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn(['average' => 0, 'count' => 0, 'min' => 0, 'max' => 0]);

        $update = ['message' => ['text' => '/stats_daily', 'chat' => ['id' => $chatId]]];

        resolve(StatsCommand::class, [
            'chatId' => (string) $chatId,
            'user'   => $user,
            'update' => $update,
        ])->handle();

        Http::assertSent(function (Request $request) use ($chatId): bool {
            return $request['chat_id'] === $chatId
                && $request['text'] === 'Немає записів за цей період :(';
        });
    });

    it('sends error message for unknown stats command', function (): void {
        Http::fake();
        $chatId = 602882484;
        $user = User::factory()->create(['telegram_chat_id' => $chatId]);

        $update = ['message' => ['text' => '/stats_yearly', 'chat' => ['id' => $chatId]]];

        resolve(StatsCommand::class, [
            'chatId' => (string) $chatId,
            'user'   => $user,
            'update' => $update,
        ])->handle();

        Http::assertSent(function (Request $request) use ($chatId): bool {
            return $request['chat_id'] === $chatId
                && str_contains($request['text'], 'Невідома команда');
        });
    });

    it('sends not linked message when user is null', function (): void {
        Http::fake();

        $update = ['message' => ['text' => '/stats_daily', 'chat' => ['id' => 999]]];

        resolve(StatsCommand::class, [
            'chatId' => '999',
            'user'   => null,
            'update' => $update,
        ])->handle();

        Http::assertSent(function (Request $request): bool {
            return str_contains($request['text'], 'не підключено');
        });
    });
});

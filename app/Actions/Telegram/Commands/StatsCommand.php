<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\MoodEntry\GetMoodStatisticAction;
use App\Actions\Telegram\Command;
use App\Actions\Telegram\SendTelegramMessage;
use App\Enums\StatsPeriod;
use App\Models\User;

class StatsCommand extends Command
{
    public function __construct(
        string $chatId,
        ?User $user,
        array $update,
        SendTelegramMessage $sendTelegramMessage,
        private readonly GetMoodStatisticAction $getMoodStatisticAction,
    ) {
        parent::__construct($chatId, $user, $update, $sendTelegramMessage);
    }

    public static function getName(): string
    {
        return '/stats_';
    }

    public static function accepts(string $commandText): bool
    {
        return str_starts_with(explode(' ', $commandText)[0], '/stats_');
    }

    public function handle(): void
    {
        if (! $this->user instanceof User) {
            $this->reply('❌ Акаунт не підключено до Mood Tracker. Використайте /start для підключення.');

            return;
        }

        $text = $this->update['message']['text'] ?? '';
        $period = StatsPeriod::fromCommand($text);

        if (! $period instanceof StatsPeriod) {
            $this->reply(
                "❌ Невідома команда. Доступні:\n"
                ."*/stats_daily*\n"
                ."*/stats_weekly*\n"
                .'*/stats_monthly*'
            );

            return;
        }

        $statistic = $this->getMoodStatisticAction->execute($this->user, $period);

        if ($statistic['count'] === 0) {
            $this->reply('Немає записів за цей період :(');

            return;
        }

        $this->reply(
            "📊 *{$period->label()}*\n"
            ."Середній настрій: *{$statistic['average']}*\n"
            ."Кількість: *{$statistic['count']}*\n"
            ."Мін: *{$statistic['min']}*\n"
            .sprintf('Макс: *%s*', $statistic['max'])
        );
    }
}

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
            $this->reply(__('messages.common.not_linked'));

            return;
        }

        $text = $this->update['message']['text'] ?? '';
        $period = StatsPeriod::fromCommand($text);

        if (! $period instanceof StatsPeriod) {
            $this->reply(__('messages.stats.unknown_command'));

            return;
        }

        $statistic = $this->getMoodStatisticAction->execute($this->user, $period);

        if ($statistic['count'] === 0) {
            $this->reply(__('messages.stats.no_entries'));

            return;
        }

        $this->reply(__('messages.stats.result', [
            'period'  => $period->label(),
            'average' => $statistic['average'],
            'count'   => $statistic['count'],
            'min'     => $statistic['min'],
            'max'     => $statistic['max'],
        ]));
    }
}

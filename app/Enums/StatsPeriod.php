<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Carbon;

enum StatsPeriod: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    public static function fromCommand(string $text): ?self
    {
        $period = str_replace('/stats_', '', $text);

        return self::tryFrom($period);
    }

    public function label(): string
    {
        return match ($this) {
            self::DAILY   => 'Mood Statistics for this day',
            self::WEEKLY  => 'Mood Statistics for the last week',
            self::MONTHLY => 'Mood Statistics for the last month',
        };
    }

    /**
     * @return Carbon[]
     */
    public function dateRange(): array
    {
        $now = now();

        return match ($this) {
            self::DAILY   => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            self::WEEKLY  => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            self::MONTHLY => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
}

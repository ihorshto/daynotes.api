<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Carbon;

enum AnalyticsPeriod: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';

    public static function fromCommand(string $command): ?self
    {
        return match (true) {
            str_contains($command, 'daily')   => self::Daily,
            str_contains($command, 'weekly')  => self::Weekly,
            str_contains($command, 'monthly') => self::Monthly,
            default                           => null,
        };
    }

    /**
     * @return Carbon[]
     */
    public function dateRange(): array
    {
        return match ($this) {
            self::Daily => [
                today(),
                now()->endOfDay(),
            ],
            self::Weekly => [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ],
            self::Monthly => [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ],
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }
}

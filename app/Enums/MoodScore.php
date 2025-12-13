<?php

declare(strict_types=1);

namespace App\Enums;

enum MoodScore: int
{
    case VERY_BAD = 1;
    case BAD = 2;
    case NEUTRAL = 3;
    case GOOD = 4;
    case VERY_GOOD = 5;

    public function label(): string
    {
        return match ($this) {
            self::VERY_BAD  => 'Very Bad',
            self::BAD       => 'Bad',
            self::NEUTRAL   => 'Neutral',
            self::GOOD      => 'Good',
            self::VERY_GOOD => 'Very Good',
        };
    }
}

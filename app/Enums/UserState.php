<?php

declare(strict_types=1);

namespace App\Enums;

enum UserState: string
{
    case Idle = 'idle';
    case WaitingForMood = 'waiting_for_mood';
    case WaitingForNote = 'waiting_for_note';
}

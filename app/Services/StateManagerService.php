<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserState;
use App\Models\User;

class StateManagerService
{
    public function get(User $user): UserState
    {
        return UserState::tryFrom(
            $user->state?->state ?? UserState::Idle->value
        );
    }

    public function getPayload(User $user): array
    {
        return $user->state?->payload ?? [];
    }

    public function set(User $user, UserState $state, array $payload = []): void
    {
        $user->state()->updateOrCreate(
            [],
            [
                'state'   => $state->value,
                'payload' => $payload,
            ]
        );
    }

    public function clear(User $user): void
    {
        $this->set($user, UserState::Idle);
    }
}

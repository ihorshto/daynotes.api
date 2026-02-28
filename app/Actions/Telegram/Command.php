<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;

abstract class Command
{
    public function __construct(
        protected readonly string $chatId,
        protected readonly ?User $user,
        protected readonly array $update,
        protected readonly SendTelegramMessage $sendTelegramMessage,
    ) {}

    abstract public static function getName(): string;

    abstract public function handle(): void;

    public static function accepts(string $commandText): bool
    {
        return explode(' ', $commandText)[0] === static::getName();
    }

    protected function reply(string $text): void
    {
        $this->sendTelegramMessage->execute((int) $this->chatId, $text);
    }
}

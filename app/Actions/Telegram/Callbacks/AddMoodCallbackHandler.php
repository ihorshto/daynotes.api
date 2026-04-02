<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Callbacks;

use App\Actions\Telegram\CallbackHandler;
use App\Actions\Telegram\Commands\AddCommand;

class AddMoodCallbackHandler extends CallbackHandler
{
    public static function accepts(string $callbackData): bool
    {
        return $callbackData === 'add_mood';
    }

    public function handle(): void
    {
        $this->acknowledge();

        resolve(AddCommand::class, [
            'chatId' => $this->chatId,
            'user'   => $this->user,
            'update' => $this->update,
        ])->handle();
    }
}

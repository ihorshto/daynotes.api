<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class CallbackRouter
{
    /** @var array<int, class-string<CallbackHandler>> */
    protected array $handlers = [];

    public function __construct()
    {
        $this->registerHandlers();
    }

    public function dispatch(string $callbackData, string $chatId, ?User $user, array $payload): void
    {
        foreach ($this->handlers as $handlerClass) {
            if ($handlerClass::accepts($callbackData)) {
                resolve($handlerClass, [
                    'chatId'   => $chatId,
                    'user'     => $user,
                    'update'   => $payload,
                ])->handle();

                return;
            }
        }
    }

    protected function registerHandlers(): void
    {
        $files = File::allFiles(app_path('Actions/Telegram/Callbacks'));

        foreach ($files as $file) {
            $class = $this->resolveClassFromFile($file);

            if (is_subclass_of($class, CallbackHandler::class)) {
                $this->handlers[] = $class;
            }
        }
    }

    protected function resolveClassFromFile(SplFileInfo $file): string
    {
        $relative = str_replace(app_path().'/', '', $file->getRealPath());
        $class = str_replace(['/', '.php'], ['\\', ''], $relative);

        return 'App\\'.$class;
    }
}

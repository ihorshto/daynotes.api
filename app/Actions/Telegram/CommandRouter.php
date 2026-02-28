<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class CommandRouter
{
    /** @var array<int, class-string<Command>> */
    protected array $commands = [];

    public function __construct()
    {
        $this->registerCommands();
    }

    public function dispatch(string $text, string $chatId, ?User $user, array $update): void
    {
        foreach ($this->commands as $commandClass) {
            if ($commandClass::accepts($text)) {
                resolve($commandClass, [
                    'chatId' => $chatId,
                    'user'   => $user,
                    'update' => $update,
                ])->handle();

                return;
            }
        }
    }

    protected function registerCommands(): void
    {
        $files = File::allFiles(app_path('Actions/Telegram/Commands'));

        foreach ($files as $file) {
            $class = $this->resolveClassFromFile($file);

            if (is_subclass_of($class, Command::class)) {
                $this->commands[] = $class;
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

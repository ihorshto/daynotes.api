<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Telegram\Commands\HandleAnalyticsCommandAction;
use App\Actions\Telegram\Commands\HandleStartCommandAction;
use App\Actions\Telegram\Commands\HandleStatsCommandAction;
use App\Actions\Telegram\Commands\HandleUnLinkCommandAction;
use Illuminate\Http\JsonResponse;

readonly class TelegramCommandsService
{
    public function __construct(
        private HandleStartCommandAction $handleStartCommandAction,
        private HandleUnLinkCommandAction $handleUnLinkCommand,
        private HandleStatsCommandAction $handleStatsCommand,
        private HandleAnalyticsCommandAction $handleAnalyticsCommand,
    ) {}

    public function getCommandResponse(array $message, $text): JsonResponse
    {
        if (str_starts_with($text, '/start')) {
            $this->handleStartCommandAction->handle($message);

            return response()->json(['ok' => true]);
        }

        if (str_starts_with($text, '/unlink')) {
            $this->handleUnLinkCommand->handle($message);

            return response()->json(['ok' => true]);
        }

        if (str_starts_with($text, '/stats_')) {
            $this->handleStatsCommand->handle($message);

            return response()->json(['ok' => true]);
        }

        if (str_starts_with($text, '/analytics_')) {
            $this->handleAnalyticsCommand->handle($message);

            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false, 'status' => 'unknown_command']);
    }
}

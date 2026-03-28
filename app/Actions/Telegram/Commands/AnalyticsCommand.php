<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\Command;
use App\Enums\AnalyticsPeriod;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AnalyticsCommand extends Command
{
    public static function getName(): string
    {
        return '/analytics_';
    }

    public static function accepts(string $commandText): bool
    {
        return str_starts_with(explode(' ', $commandText)[0], '/analytics_');
    }

    public function handle(): void
    {
        if (! $this->user instanceof User) {
            $this->reply(__('messages.common.not_linked'));

            return;
        }

        $text = $this->update['message']['text'] ?? '';
        $period = AnalyticsPeriod::fromCommand($text);

        if (! $period instanceof AnalyticsPeriod) {
            $this->reply(__('messages.analytics.unknown_command'));

            return;
        }

        [$from, $to] = $period->dateRange();

        $this->reply(__('messages.analytics.generating', ['period' => $period->label()]));

        $response = Http::post(
            config('services.n8n.url').'/webhook/mood-analytics',
            [
                'user_id'   => $this->user->id,
                'from_date' => $from->toDateString(),
                'to_date'   => $to->toDateString(),
                'period'    => $period->value,
            ]
        );

        if (! $response->successful()) {
            $this->reply(__('messages.analytics.service_unavailable'));

            return;
        }

        $this->reply($response->json('output') ?? __('messages.analytics.unavailable'));
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Telegram\Commands;

use App\Actions\Telegram\Command;
use App\Enums\AnalyticsPeriod;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        Log::info(sprintf('Requesting analytics for user %s from %s to %s for period %s', $this->user->id, $from->toDateString(), $to->toDateString(), $period->value).(' with language '.$this->user->lang->value));
        Log::info('N8N URL: '.config('services.n8n.url'));
        Log::info('N8N Webhook URL: '.config('services.n8n.url').'/webhook/mood-analytics');
        $response = Http::post(
            config('services.n8n.url').'/webhook/mood-analytics',
            [
                'user_id'   => $this->user->id,
                'from_date' => $from->toDateString(),
                'to_date'   => $to->toDateString(),
                'lang'      => $this->user->lang->value,
                'period'    => $period->value,
            ]
        );

        Log::info('N8N Response: '.$response->body());

        if (! $response->successful()) {
            $this->reply(__('messages.analytics.service_unavailable'));

            return;
        }

        $this->reply($response->json('output') ?? __('messages.analytics.unavailable'));
    }
}

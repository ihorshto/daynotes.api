<?php

declare(strict_types=1);

use App\Actions\Telegram\CallbackRouter;
use App\Enums\UserState;
use App\Models\User;
use App\Services\StateManagerService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Cache::flush();
});

describe('Webhook Flow Lock', function (): void {

    it('sets a per-message lock when add_mood is first processed', function (): void {
        $this->mock(CallbackRouter::class)->shouldReceive('dispatch')->once();

        User::factory()->create(['telegram_chat_id' => '111111', 'lang' => 'en']);

        $this->postJson('/api/telegram/webhook', [
            'callback_query' => [
                'id'      => 'q1',
                'data'    => 'add_mood',
                'message' => [
                    'message_id' => 100,
                    'date'       => now()->timestamp,
                    'chat'       => ['id' => 111111],
                ],
            ],
        ])->assertSuccessful();

        expect(Cache::has('callback-lock:111111:100'))->toBeTrue();
    });

    it('silently ignores add_mood re-click on the same message', function (): void {
        $router = $this->mock(CallbackRouter::class);
        $router->shouldReceive('dispatch')->once();

        User::factory()->create(['telegram_chat_id' => '111111', 'lang' => 'en']);

        $payload = [
            'callback_query' => [
                'id'      => 'q1',
                'data'    => 'add_mood',
                'message' => [
                    'message_id' => 100,
                    'date'       => now()->timestamp,
                    'chat'       => ['id' => 111111],
                ],
            ],
        ];

        $this->postJson('/api/telegram/webhook', $payload)->assertSuccessful();
        $this->postJson('/api/telegram/webhook', $payload)->assertSuccessful();
    });

    it('allows add_mood from a new notification while a flow is active', function (): void {
        $router = $this->mock(CallbackRouter::class);
        $router->shouldReceive('dispatch')->twice();

        User::factory()->create(['telegram_chat_id' => '111111', 'lang' => 'en']);

        $this->postJson('/api/telegram/webhook', [
            'callback_query' => [
                'id'      => 'q1',
                'data'    => 'add_mood',
                'message' => [
                    'message_id' => 100,
                    'date'       => now()->timestamp,
                    'chat'       => ['id' => 111111],
                ],
            ],
        ])->assertSuccessful();

        $this->postJson('/api/telegram/webhook', [
            'callback_query' => [
                'id'      => 'q2',
                'data'    => 'add_mood',
                'message' => [
                    'message_id' => 200,
                    'date'       => now()->timestamp,
                    'chat'       => ['id' => 111111],
                ],
            ],
        ])->assertSuccessful();
    });

    it('silently ignores mood score re-click on the same message', function (): void {
        $router = $this->mock(CallbackRouter::class);
        $router->shouldReceive('dispatch')->once();

        User::factory()->create(['telegram_chat_id' => '111111', 'lang' => 'en']);

        $payload = [
            'callback_query' => [
                'id'      => 'q1',
                'data'    => 'mood:5',
                'message' => [
                    'message_id' => 300,
                    'date'       => now()->timestamp,
                    'chat'       => ['id' => 111111],
                ],
            ],
        ];

        $this->postJson('/api/telegram/webhook', $payload)->assertSuccessful();
        $this->postJson('/api/telegram/webhook', $payload)->assertSuccessful();
    });

    it('keeps the lock after skip_note so the mood keyboard cannot be reused', function (): void {
        Http::fake();

        $user = User::factory()->create(['telegram_chat_id' => '111111', 'lang' => 'en']);
        resolve(StateManagerService::class)->set($user, UserState::WaitingForNote, ['mood_score' => 5]);
        Cache::put('callback-lock:111111:100', true, 1800);

        $this->postJson('/api/telegram/webhook', [
            'callback_query' => [
                'id'      => 'q1',
                'data'    => 'skip_note',
                'message' => [
                    'message_id' => 200,
                    'date'       => now()->timestamp,
                    'chat'       => ['id' => 111111],
                ],
            ],
        ])->assertSuccessful();

        expect(Cache::has('callback-lock:111111:100'))->toBeTrue();
    });

    it('keeps the lock after text note so the mood keyboard cannot be reused', function (): void {
        Http::fake();

        $user = User::factory()->create(['telegram_chat_id' => '111111', 'lang' => 'en']);
        resolve(StateManagerService::class)->set($user, UserState::WaitingForNote, ['mood_score' => 5]);
        Cache::put('callback-lock:111111:100', true, 1800);

        $this->postJson('/api/telegram/webhook', [
            'message' => [
                'date' => now()->timestamp,
                'text' => 'Had a great day!',
                'chat' => ['id' => 111111],
            ],
        ])->assertSuccessful();

        expect(Cache::has('callback-lock:111111:100'))->toBeTrue();
    });
});

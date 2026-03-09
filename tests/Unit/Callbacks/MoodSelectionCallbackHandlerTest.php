<?php

declare(strict_types=1);

use App\Actions\Telegram\Callbacks\MoodSelectionCallbackHandler;
use App\Enums\UserState;
use App\Models\User;
use App\Services\StateManagerService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

describe('MoodSelectionCallbackHandler', function (): void {

    describe('accepts()', function (): void {
        it('accepts callback data prefixed with mood:', function (string $callbackData): void {
            expect(MoodSelectionCallbackHandler::accepts($callbackData))->toBeTrue();
        })->with([
            'mood:1' => 'mood:1',
            'mood:3' => 'mood:3',
            'mood:5' => 'mood:5',
        ]);

        it('rejects callback data that does not start with mood:', function (string $callbackData): void {
            expect(MoodSelectionCallbackHandler::accepts($callbackData))->toBeFalse();
        })->with([
            'skip_note' => 'skip_note',
            '/add'      => '/add',
            'mood_1'    => 'mood_1',
        ]);
    });

    describe('handle()', function (): void {
        it('sets WaitingForNote state with correct mood_score and replies with keyboard', function (): void {
            Http::fake();
            $chatId = 602882484;
            $user = User::factory()->create(['telegram_chat_id' => $chatId]);

            $stateManager = $this->mock(StateManagerService::class);
            $stateManager->shouldReceive('set')
                ->once()
                ->with($user, UserState::WaitingForNote, ['mood_score' => 3]);

            $update = [
                'callback_query' => [
                    'id'      => 'cb_123',
                    'data'    => 'mood:3',
                    'message' => ['chat' => ['id' => $chatId]],
                ],
            ];

            resolve(MoodSelectionCallbackHandler::class, [
                'chatId' => (string) $chatId,
                'user'   => $user,
                'update' => $update,
            ])->handle();

            Http::assertSent(function (Request $request) use ($chatId): bool {
                return str_contains($request->url(), 'sendMessage')
                    && $request['chat_id'] === $chatId
                    && str_contains($request['text'], '3');
            });
        });

        it('stores the correct mood_score in state payload', function (string $callbackData, int $expectedScore): void {
            Http::fake();
            $chatId = 602882484;
            $user = User::factory()->create(['telegram_chat_id' => $chatId]);

            $stateManager = $this->mock(StateManagerService::class);
            $stateManager->shouldReceive('set')
                ->once()
                ->with($user, UserState::WaitingForNote, ['mood_score' => $expectedScore]);

            $update = [
                'callback_query' => [
                    'id'      => 'cb_abc',
                    'data'    => $callbackData,
                    'message' => ['chat' => ['id' => $chatId]],
                ],
            ];

            resolve(MoodSelectionCallbackHandler::class, [
                'chatId' => (string) $chatId,
                'user'   => $user,
                'update' => $update,
            ])->handle();

            Http::assertSent(function (Request $request) use ($chatId, $expectedScore): bool {
                return str_contains($request->url(), 'sendMessage')
                    && $request['chat_id'] === $chatId
                    && str_contains($request['text'], (string) $expectedScore);
            });
        })->with([
            'mood:1' => ['mood:1', 1],
            'mood:5' => ['mood:5', 5],
        ]);

        it('sends the skip note prompt and mood score in reply text', function (): void {
            Http::fake();
            $chatId = 602882484;
            $user = User::factory()->create(['telegram_chat_id' => $chatId]);

            $this->mock(StateManagerService::class)
                ->shouldReceive('set')
                ->once();

            $update = [
                'callback_query' => [
                    'id'      => 'cb_123',
                    'data'    => 'mood:3',
                    'message' => ['chat' => ['id' => $chatId]],
                ],
            ];

            resolve(MoodSelectionCallbackHandler::class, [
                'chatId' => (string) $chatId,
                'user'   => $user,
                'update' => $update,
            ])->handle();

            Http::assertSent(function (Request $request) use ($chatId): bool {
                return str_contains($request->url(), 'sendMessage')
                    && $request['chat_id'] === $chatId
                    && str_contains($request['text'], 'зафіксовано')
                    && str_contains($request['text'], 'нотатку');
            });
        });

        it('sends not linked message and does not set state when user is null', function (): void {
            Http::fake();

            $this->mock(StateManagerService::class)
                ->shouldNotReceive('set');

            $update = [
                'callback_query' => [
                    'id'      => 'cb_999',
                    'data'    => 'mood:3',
                    'message' => ['chat' => ['id' => 999]],
                ],
            ];

            resolve(MoodSelectionCallbackHandler::class, [
                'chatId' => '999',
                'user'   => null,
                'update' => $update,
            ])->handle();

            Http::assertSent(function (Request $request): bool {
                return str_contains($request->url(), 'sendMessage')
                    && str_contains($request['text'], 'не підключено');
            });
        });
    });
});

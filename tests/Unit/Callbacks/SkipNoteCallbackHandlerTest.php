<?php

declare(strict_types=1);

use App\Actions\Telegram\Callbacks\SkipNoteCallbackHandler;
use App\Models\MoodEntry;
use App\Models\User;
use App\Services\StateManagerService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

describe('SkipNoteCallbackHandler', function (): void {

    describe('accepts()', function (): void {
        it('accepts the skip_note callback data', function (): void {
            expect(SkipNoteCallbackHandler::accepts('skip_note'))->toBeTrue();
        });

        it('rejects callback data that is not skip_note', function (string $callbackData): void {
            expect(SkipNoteCallbackHandler::accepts($callbackData))->toBeFalse();
        })->with([
            'mood:3' => 'mood:3',
            '/add'   => '/add',
        ]);
    });

    describe('handle()', function (): void {
        it('creates a MoodEntry with the mood_score from state payload and null note', function (): void {
            Http::fake();
            $chatId = 602882484;
            $user = User::factory()->create(['telegram_chat_id' => $chatId]);

            $this->mock(StateManagerService::class)
                ->shouldReceive('getPayload')
                ->once()
                ->with($user)
                ->andReturn(['mood_score' => 4])
                ->shouldReceive('clear')
                ->once()
                ->with($user);

            $update = [
                'callback_query' => [
                    'id'      => 'cb_123',
                    'data'    => 'skip_note',
                    'message' => ['chat' => ['id' => $chatId]],
                ],
            ];

            resolve(SkipNoteCallbackHandler::class, [
                'chatId' => (string) $chatId,
                'user'   => $user,
                'update' => $update,
            ])->handle();

            $entry = MoodEntry::query()
                ->where('user_id', $user->id)
                ->first();

            expect($entry)->not->toBeNull()
                ->and($entry->mood_score->value)->toBe(4)
                ->and($entry->note)->toBeNull();
        });

        it('clears user state after saving the mood entry', function (): void {
            Http::fake();
            $chatId = 602882484;
            $user = User::factory()->create(['telegram_chat_id' => $chatId]);

            $stateManager = $this->mock(StateManagerService::class);
            $stateManager->shouldReceive('getPayload')
                ->once()
                ->andReturn(['mood_score' => 2]);
            $stateManager->shouldReceive('clear')
                ->once()
                ->with($user);

            $update = [
                'callback_query' => [
                    'id'      => 'cb_123',
                    'data'    => 'skip_note',
                    'message' => ['chat' => ['id' => $chatId]],
                ],
            ];

            resolve(SkipNoteCallbackHandler::class, [
                'chatId' => (string) $chatId,
                'user'   => $user,
                'update' => $update,
            ])->handle();
        });

        it('sends confirmation reply after saving mood entry', function (): void {
            Http::fake();
            $chatId = 602882484;
            $user = User::factory()->create(['telegram_chat_id' => $chatId]);

            $this->mock(StateManagerService::class)
                ->shouldReceive('getPayload')
                ->once()
                ->andReturn(['mood_score' => 4])
                ->shouldReceive('clear')
                ->once();

            $update = [
                'callback_query' => [
                    'id'      => 'cb_123',
                    'data'    => 'skip_note',
                    'message' => ['chat' => ['id' => $chatId]],
                ],
            ];

            resolve(SkipNoteCallbackHandler::class, [
                'chatId' => (string) $chatId,
                'user'   => $user,
                'update' => $update,
            ])->handle();

            Http::assertSent(function (Request $request) use ($chatId): bool {
                return str_contains($request->url(), 'sendMessage')
                    && $request['chat_id'] === $chatId
                    && str_contains($request['text'], 'збережено');
            });
        });

        it('sends not linked message and does not create a MoodEntry when user is null', function (): void {
            Http::fake();

            $this->mock(StateManagerService::class)
                ->shouldNotReceive('getPayload')
                ->shouldNotReceive('clear');

            $update = [
                'callback_query' => [
                    'id'      => 'cb_999',
                    'data'    => 'skip_note',
                    'message' => ['chat' => ['id' => 999]],
                ],
            ];

            resolve(SkipNoteCallbackHandler::class, [
                'chatId' => '999',
                'user'   => null,
                'update' => $update,
            ])->handle();

            expect(MoodEntry::query()->count())->toBe(0);

            Http::assertSent(function (Request $request): bool {
                return str_contains($request->url(), 'sendMessage')
                    && str_contains($request['text'], 'не підключено');
            });
        });
    });
});

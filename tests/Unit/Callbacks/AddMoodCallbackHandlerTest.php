<?php

declare(strict_types=1);

use App\Actions\Telegram\Callbacks\AddMoodCallbackHandler;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

describe('AddMoodCallbackHandler', function (): void {

    describe('accepts', function (): void {
        it('accepts "add_mood" callback data', function (): void {
            expect(AddMoodCallbackHandler::accepts('add_mood'))->toBeTrue();
        });

        it('rejects other callback data', function (string $callbackData): void {
            expect(AddMoodCallbackHandler::accepts($callbackData))->toBeFalse();
        })->with([
            'skip_note' => 'skip_note',
            'mood:1'    => 'mood:1',
            '/add'      => '/add',
        ]);
    });

    describe('handle()', function (): void {
        it('successfully sends mood keyboard', function (): void {
            Http::fake();
            $chatId = 602882484;

            $user = User::factory()->create(['telegram_chat_id' => $chatId]);

            $update = [
                'callback_query' => [
                    'id'      => 'cb_123',
                    'data'    => 'add_mood',
                    'message' => ['chat' => ['id' => $chatId]],
                ],
            ];

            resolve(AddMoodCallbackHandler::class, [
                'chatId' => (string) $chatId,
                'user'   => $user,
                'update' => $update,
            ])->handle();

            Http::assertSent(function (Request $request): bool {
                return str_contains($request->url(), 'sendMessage')
                    && str_contains($request['text'], 'feeling');
            });
        });
    });
});

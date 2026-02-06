<?php

declare(strict_types=1);

use App\Actions\Telegram\SendTelegramMessage;
use App\Exceptions\TelegramMessageException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

describe('SendTelegramMessage Action', function (): void {
    it('sends a message successfully', function (): void {
        Http::fake();

        $chatId = 123456789;
        $testMessage = 'Test message';

        $sendTelegramMessage = new SendTelegramMessage;
        $sendTelegramMessage->execute($chatId, $testMessage);

        Http::assertSent(function (array $request) use ($chatId, $testMessage): bool {
            return $request->url() === 'https://api.telegram.org/bot'.config('services.telegram-bot-api.token').'/sendMessage'
                   && $request->method() === 'POST'
                   && $request['chat_id'] === $chatId
                   && $request['text'] === $testMessage;
        });
    });

    it('throws exception when Telegram API returns error', function (): void {
        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok'          => false,
                'error_code'  => 400,
                'description' => 'Bad Request: chat not found',
            ], Response::HTTP_BAD_REQUEST),
        ]);

        $chatId = 123456789;
        $testMessage = 'Test message';

        $sendTelegramMessage = new SendTelegramMessage;
        $sendTelegramMessage->execute($chatId, $testMessage);
    })->throws(TelegramMessageException::class);
});

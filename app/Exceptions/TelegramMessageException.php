<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class TelegramMessageException extends Exception
{
    public function __construct(
        public readonly int $chatId,
        public readonly ?int $errorCode = null,
        public readonly ?string $description = null,
        ?Exception $previous = null,
    ) {
        $message = sprintf(
            'Failed to send Telegram message to chat %d: %s',
            $chatId,
            $description ?? 'Unknown error'
        );

        parent::__construct($message, $errorCode ?? 0, $previous);
    }

    public static function fromResponse(int $chatId, Response $response, ?Exception $previous = null): self
    {
        $body = $response->json();

        return new self(
            chatId: $chatId,
            errorCode: $body['error_code'] ?? $response->status(),
            description: $body['description'] ?? $response->body(),
            previous: $previous,
        );
    }
}

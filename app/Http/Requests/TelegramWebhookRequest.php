<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TelegramWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message.text'            => ['required', 'string'],
            'message.chat.id'         => ['required', 'integer'],
            'message.from.username'   => ['sometimes', 'string'],
            'message.from.first_name' => ['sometimes', 'string'],
            'message.date'            => ['required', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.text.required'          => 'The message text is required.',
            'message.chat.id.required'       => 'The chat ID is required.',
            'message.from.username.string'   => 'The username must be a string.',
            'message.from.first_name.string' => 'The first name must be a string.',
            'message.date.required'          => 'The message date is required.',
        ];
    }
}

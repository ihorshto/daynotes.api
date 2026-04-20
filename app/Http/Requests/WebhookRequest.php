<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message'                        => ['nullable', 'array'],
            'message.date'                   => ['required_with:message', 'integer'],
            'message.text'                   => ['nullable', 'string', 'max:4096'],
            'message.chat'                   => ['required_with:message', 'array'],
            'message.chat.id'                => ['required_with:message', 'integer'],

            'callback_query'                            => ['nullable', 'array'],
            'callback_query.id'                         => ['required_with:callback_query', 'string'],
            'callback_query.data'                       => ['required_with:callback_query', 'string', 'max:64'],
            'callback_query.message'                    => ['required_with:callback_query', 'array'],
            'callback_query.message.message_id'         => ['nullable', 'integer'],
            'callback_query.message.date'               => ['required_with:callback_query', 'integer'],
            'callback_query.message.chat'               => ['required_with:callback_query', 'array'],
            'callback_query.message.chat.id'            => ['required_with:callback_query', 'integer'],
        ];
    }
}

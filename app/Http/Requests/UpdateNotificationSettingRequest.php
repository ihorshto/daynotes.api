<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingRequest extends FormRequest
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
            'morning_time'      => ['nullable', 'date_format:H:i'],
            'afternoon_time'    => ['nullable', 'date_format:H:i'],
            'evening_time'      => ['nullable', 'date_format:H:i'],
            'morning_enabled'   => ['nullable', 'boolean'],
            'afternoon_enabled' => ['nullable', 'boolean'],
            'evening_enabled'   => ['nullable', 'boolean'],
            'email_enabled'     => ['nullable', 'boolean'],
            'telegram_enabled'  => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'morning_time.date_format'   => 'The morning time must be in the format HH:MM.',
            'afternoon_time.date_format' => 'The afternoon time must be in the format HH:MM.',
            'evening_time.date_format'   => 'The evening time must be in the format HH:MM.',
            'morning_enabled.boolean'    => 'The morning enabled field must be true or false.',
            'afternoon_enabled.boolean'  => 'The afternoon enabled field must be true or false.',
            'evening_enabled.boolean'    => 'The evening enabled field must be true or false.',
        ];
    }
}

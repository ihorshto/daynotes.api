<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserNotificationSettingRequest extends FormRequest
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
            'time'              => ['required', 'date_format:H:i'],
            'email_enabled'     => ['required', 'boolean'],
            'telegram_enabled'  => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'time.required'             => 'Notification time is required.',
            'time.date_format'          => 'Notification time must be in the format HH:MM.',
            'email_enabled.required'    => 'Email notification setting is required.',
            'email_enabled.boolean'     => 'Email notification setting must be true or false.',
            'telegram_enabled.required' => 'Telegram notification setting is required.',
            'telegram_enabled.boolean'  => 'Telegram notification setting must be true or false.',
        ];
    }
}

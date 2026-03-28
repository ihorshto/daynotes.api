<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Lang;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserLangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lang' => ['required', new Enum(Lang::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lang.required'                         => 'Language is required.',
            'lang.Illuminate\Validation\Rules\Enum' => 'The selected language is invalid. Allowed values: en, uk, fr.',
        ];
    }
}

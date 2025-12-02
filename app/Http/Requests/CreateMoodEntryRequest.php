<?php

namespace App\Http\Requests;

use App\Enums\MoodScore;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateMoodEntryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mood_score' => ['required', new Enum(MoodScore::class)],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'mood_score.required' => 'The mood score is required.',
            'mood_score.enum' => 'The selected mood score is invalid.',
            'note.string' => 'The note must be a string.',
            'note.max' => 'The note may not be greater than 2000 characters.',
        ];
    }
}

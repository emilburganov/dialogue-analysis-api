<?php

namespace App\Http\Requests\API\Dialogue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDialogueResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'result' => ['required', Rule::in(['bought', 'not_bought'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'result.required' => 'Укажите результат диалога.',
            'result.in' => 'Недопустимый результат диалога.',
        ];
    }
}

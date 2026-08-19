<?php

namespace App\Http\Requests\API\Analysis;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnalysisRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'description' => is_string($this->description) ? trim($this->description) : $this->description,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'default_severity' => ['sometimes', Rule::in(['high', 'medium', 'low'])],
            'is_enabled' => ['sometimes', 'boolean'],
            'config' => ['sometimes', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Укажите название правила.',
            'name.max' => 'Название не должно превышать 255 символов.',
            'description.required' => 'Укажите описание правила.',
            'description.max' => 'Описание не должно превышать 5000 символов.',
            'default_severity.in' => 'Недопустимый уровень критичности.',
            'config.array' => 'Параметры правила должны быть объектом.',
        ];
    }
}

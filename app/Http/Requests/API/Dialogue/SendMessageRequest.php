<?php

namespace App\Http\Requests\API\Dialogue;

use App\Services\Dialogue\DTO\SendMessageDTO;
use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
        ];
    }

    public function toDto(): SendMessageDTO
    {
        return SendMessageDTO::fromArray($this->validated());
    }
}

<?php

namespace App\Http\Requests\Response;

use Illuminate\Foundation\Http\FormRequest;

class CreateResponseRequest extends FormRequest
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
            'survey_id' => 'required|integer|min:1',
            'metadata' => 'sometimes|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'survey_id.required' => 'Survey ID is required.',
            'survey_id.min' => 'Survey ID must be at least 1.',
        ];
    }

    /**
     * Get body parameters for API documentation
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'survey_id' => [
                'description' => 'ID of the survey to respond to. Must be at least 1.',
                'example' => 1,
                'required' => true,
            ],
            'metadata' => [
                'description' => 'Response metadata (IP, user_agent, etc.).',
                'example' => ['ip' => '192.168.1.1', 'user_agent' => 'Mozilla/5.0...'],
                'required' => false,
            ],
        ];
    }
} 
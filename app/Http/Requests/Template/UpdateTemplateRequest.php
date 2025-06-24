<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Will be checked in service layer
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'sometimes|boolean',
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
            'title.required' => 'Template title is required.',
            'title.max' => 'Template title cannot exceed 255 characters.',
            'description.max' => 'Template description cannot exceed 1000 characters.',
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
            'title' => [
                'description' => 'Template title',
                'example' => 'Updated Customer Satisfaction Survey',
                'required' => false,
            ],
            'description' => [
                'description' => 'Template description',
                'example' => 'An updated comprehensive survey to measure customer satisfaction',
                'required' => false,
            ],
            'is_public' => [
                'description' => 'Whether the template is public',
                'example' => true,
                'required' => false,
            ],
        ];
    }
} 
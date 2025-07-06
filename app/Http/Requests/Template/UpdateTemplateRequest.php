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
            'title' => 'sometimes|string|max:255',
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
                'description' => 'The new title for the template. Maximum 255 characters.',
                'example' => 'Updated Employee Feedback Template',
                'required' => false,
            ],
            'description' => [
                'description' => 'The new description for the template. Maximum 1000 characters.',
                'example' => 'An updated template for the quarterly employee feedback process.',
                'required' => false,
            ],
            'is_public' => [
                'description' => 'Update the public availability of the template.',
                'example' => true,
                'required' => false,
            ],
        ];
    }
} 
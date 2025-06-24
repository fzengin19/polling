<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;

class CreateTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authenticated users can create templates
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'forked_from_template_id' => 'nullable|integer',
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
            'forked_from_template_id.exists' => 'The selected template to fork from does not exist.',
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
                'example' => 'Customer Satisfaction Survey',
                'required' => true,
            ],
            'description' => [
                'description' => 'Template description',
                'example' => 'A comprehensive survey to measure customer satisfaction',
                'required' => false,
            ],
            'is_public' => [
                'description' => 'Whether the template is public',
                'example' => false,
                'required' => false,
            ],
            'forked_from_template_id' => [
                'description' => 'ID of the template to fork from',
                'example' => 1,
                'required' => false,
            ],
        ];
    }
} 
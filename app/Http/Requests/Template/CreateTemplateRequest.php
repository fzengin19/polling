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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'forked_from_template_id' => 'nullable|integer|min:1',
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
            'forked_from_template_id.min' => 'Forked template ID must be at least 1.',
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
                'description' => 'The title of the template. Maximum 255 characters.',
                'example' => 'New Employee Onboarding Checklist',
                'required' => true,
            ],
            'description' => [
                'description' => 'A brief description of the template. Maximum 1000 characters.',
                'example' => 'A comprehensive template for the onboarding process of new hires.',
                'required' => false,
            ],
            'is_public' => [
                'description' => 'Whether the template should be publicly available for others to use.',
                'example' => false,
                'required' => false,
            ],
            'forked_from_template_id' => [
                'description' => 'The ID of the template this was forked from, if any.',
                'example' => null,
                'required' => false,
            ],
        ];
    }
} 
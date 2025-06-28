<?php

namespace App\Http\Requests\Survey;

use Illuminate\Foundation\Http\FormRequest;

class CreateSurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authenticated users can create surveys
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
            'status' => 'required|in:draft,active,archived',
            'template_id' => 'nullable|integer|min:1',
            'template_version_id' => 'nullable|integer|min:1',
            'settings' => 'nullable|array',
            'expires_at' => 'nullable|date|after:now',
            'max_responses' => 'nullable|integer|min:1|max:1000000',
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
            'title.required' => 'Survey title is required.',
            'title.max' => 'Survey title cannot exceed 255 characters.',
            'description.max' => 'Survey description cannot exceed 1000 characters.',
            'status.in' => 'Status must be draft, active, or archived.',
            'template_id.min' => 'Template ID must be at least 1.',
            'template_version_id.min' => 'Template version ID must be at least 1.',
            'expires_at.after' => 'Expiration date must be in the future.',
            'max_responses.min' => 'Maximum responses must be at least 1.',
            'max_responses.max' => 'Maximum responses cannot exceed 1,000,000.',
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
                'description' => 'Survey title',
                'example' => 'Customer Satisfaction Survey 2024',
                'required' => true,
            ],
            'description' => [
                'description' => 'Survey description',
                'example' => 'A comprehensive survey to measure customer satisfaction levels',
                'required' => false,
            ],
            'status' => [
                'description' => 'Survey status',
                'example' => 'draft',
                'required' => true,
            ],
            'template_id' => [
                'description' => 'ID of the template to base this survey on',
                'example' => 1,
                'required' => false,
            ],
            'template_version_id' => [
                'description' => 'ID of the specific template version to use',
                'example' => 1,
                'required' => false,
            ],
            'settings' => [
                'description' => 'Survey settings (anonymous, multiple responses, etc.)',
                'example' => ['anonymous' => true, 'multiple_responses' => false],
                'required' => false,
            ],
            'expires_at' => [
                'description' => 'Survey expiration date',
                'example' => '2024-12-31 23:59:59',
                'required' => false,
            ],
            'max_responses' => [
                'description' => 'Maximum number of responses allowed',
                'example' => 1000,
                'required' => false,
            ],
        ];
    }
} 
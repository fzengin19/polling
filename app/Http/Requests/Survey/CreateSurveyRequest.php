<?php

namespace App\Http\Requests\Survey;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'settings.anonymous' => 'nullable|boolean',
            'settings.multiple_responses' => 'nullable|boolean',
            'settings.ui_complexity_level' => 'nullable|string|in:basic,intermediate,advanced',
            'expires_at' => 'nullable|date|after:now',
            'max_responses' => 'nullable|integer|min:1|max:1000000',

            // Theme Validation Rules
            'settings.theme' => 'nullable|array',
            'settings.theme.primary_color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'settings.theme.font' => ['nullable', 'string', Rule::in(['Arial', 'Georgia', 'Lato', 'Roboto', 'Verdana'])],
            'settings.theme.logo_media_id' => 'nullable|integer|exists:media,id',
            'settings.theme.logo_placement' => ['nullable', 'string', Rule::in(['top', 'bottom', 'top-left', 'top-right', 'bottom-left', 'bottom-right'])],
            'settings.theme.background_color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
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
            'settings.theme.primary_color.regex' => 'The primary color must be a valid hex code.',
            'settings.theme.font.in' => 'The selected font is not valid.',
            'settings.theme.logo_media_id.exists' => 'The selected logo media ID is not valid.',
            'settings.theme.logo_placement.in' => 'The logo placement must be one of: top, bottom, top-left, top-right, bottom-left, bottom-right.',
            'settings.theme.background_color.regex' => 'The background color must be a valid hex code.',
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
                'description' => 'The title of the survey.',
                'example' => 'Customer Satisfaction Survey',
            ],
            'description' => [
                'description' => 'A brief description of the survey.',
                'example' => 'Please provide your valuable feedback.',
            ],
            'status' => [
                'description' => 'The status of the survey.',
                'example' => 'draft',
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
                'description' => 'Survey settings (anonymous, complexity, theming, etc.)',
                'example' => [
                    'anonymous' => true,
                    'multiple_responses' => false, 
                    'ui_complexity_level' => 'basic',
                    'theme' => [
                        'primary_color' => '#3B82F6',
                        'font' => 'Roboto',
                        'logo_media_id' => 1,
                        'background_color' => '#F9FAFB'
                    ]
                ],
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
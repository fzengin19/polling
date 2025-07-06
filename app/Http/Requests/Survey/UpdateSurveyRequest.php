<?php

namespace App\Http\Requests\Survey;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSurveyRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', 'string', Rule::in(['draft', 'active', 'archived'])],
            'settings' => 'sometimes|array',
            'settings.anonymous' => 'sometimes|boolean',
            'settings.multiple_responses' => 'sometimes|boolean',
            'settings.theme.primary_color' => ['sometimes', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'settings.theme.font' => ['sometimes', 'string', Rule::in(['Arial', 'Georgia', 'Lato', 'Roboto', 'Verdana'])],
            'settings.theme.logo_media_id' => 'nullable|integer|exists:media,id',
            'settings.theme.logo_placement' => ['sometimes', 'string', Rule::in(['top', 'bottom', 'top-left', 'top-right', 'bottom-left', 'bottom-right'])],
            'settings.theme.background_color' => ['sometimes', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
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
                'description' => 'Survey title',
                'example' => 'Updated Customer Satisfaction Survey 2024',
                'required' => false,
            ],
            'description' => [
                'description' => 'Survey description',
                'example' => 'An updated comprehensive survey to measure customer satisfaction levels',
                'required' => false,
            ],
            'status' => [
                'description' => 'Survey status',
                'example' => 'active',
                'required' => false,
            ],
            'settings' => [
                'description' => 'Survey settings (anonymous, complexity, theming, etc.)',
                'example' => [
                    'theme' => [
                        'primary_color' => '#10B981',
                        'font' => 'Lato'
                    ]
                ],
                'required' => false,
            ],
            'expires_at' => [
                'description' => 'Survey expiration date',
                'example' => '2025-01-01 00:00:00',
                'required' => false,
            ],
            'max_responses' => [
                'description' => 'Maximum number of responses allowed',
                'example' => 500,
                'required' => false,
            ],
        ];
    }
} 
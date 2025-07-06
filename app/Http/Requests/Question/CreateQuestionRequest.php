<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateQuestionRequest extends FormRequest
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
        $rules = [
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'is_required' => 'boolean',
            'help_text' => 'nullable|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'order_index' => 'integer|min:0',
        ];

        // Add conditional validation for config based on question type
        $rules = array_merge($rules, $this->getConditionalConfigRules());
        
        return $rules;
    }

    protected function getConditionalConfigRules(): array
    {
        $type = $this->input('type');

        if ($type === 'number') {
            return [
                'config.min' => ['nullable', 'integer'],
                'config.max' => ['nullable', 'integer', 'gte:config.min'],
            ];
        }

        if ($type === 'linear_scale') {
            return [
                'config.min' => ['nullable', 'integer'],
                'config.max' => ['nullable', 'integer', 'gte:config.min'],
                'config.label_min' => ['nullable', 'string', 'max:50'],
                'config.label_max' => ['nullable', 'string', 'max:50'],
            ];
        }

        return [];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'type' => [
                'description' => 'The type of the question (e.g., text, number, linear_scale).',
                'example' => 'number',
                'required' => true,
            ],
            'title' => [
                'description' => 'The question text itself. Maximum 255 characters.',
                'example' => 'How would you rate our service?',
                'required' => true,
            ],
            'is_required' => [
                'description' => 'Whether the user must answer this question.',
                'example' => false,
                'required' => false,
            ],
            'help_text' => [
                'description' => 'Additional text to help the user answer. Maximum 255 characters.',
                'example' => 'Please provide a rating from 1 to 10.',
                'required' => false,
            ],
            'placeholder' => [
                'description' => 'Placeholder text for input fields. Most useful for text, email, url, phone types.',
                'example' => 'Enter your answer here',
                'required' => false,
            ],
            'config' => [
                'description' => 'Question-specific configuration. Depends on the question type.',
                'example' => [
                    'min' => 1,
                    'max' => 10,
                ],
                'required' => false,
            ],
        ];
    }
} 
<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => 'sometimes|required|string|max:50',
            'title' => 'sometimes|required|string|max:255',
            'is_required' => 'sometimes|boolean',
            'help_text' => 'nullable|string|max:1000',
            'placeholder' => 'nullable|string|max:255',
            'config' => 'sometimes|array',
            'order_index' => 'sometimes|integer|min:0',
        ];
        
        // Add conditional validation for config based on question type
        $rules = array_merge($rules, $this->getConditionalConfigRules());

        return $rules;
    }

    protected function getConditionalConfigRules(): array
    {
        // Only apply these rules if the 'config' key exists in the request
        if (!$this->has('config')) {
            return [];
        }

        $type = $this->input('type', $this->route('question') ? $this->route('question')->type : null);

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

    public function messages(): array
    {
        return [
            'type.required' => 'Question type is required.',
            'type.max' => 'Question type cannot exceed 50 characters.',
            'title.required' => 'Question title is required.',
            'title.max' => 'Question title cannot exceed 255 characters.',
            'help_text.max' => 'Help text cannot exceed 1000 characters.',
            'placeholder.max' => 'Placeholder cannot exceed 255 characters.',
            'order_index.min' => 'Order index must be at least 0.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'type' => [
                'description' => 'Question type (text, multiple_choice, rating, etc.)',
                'example' => 'multiple_choice',
                'required' => false,
            ],
            'title' => [
                'description' => 'Question title',
                'example' => 'Updated question title',
                'required' => false,
            ],
            'is_required' => [
                'description' => 'Whether the question is required',
                'example' => true,
                'required' => false,
            ],
            'help_text' => [
                'description' => 'Help text for the question',
                'example' => 'Updated help text',
                'required' => false,
            ],
            'placeholder' => [
                'description' => 'Placeholder text for input fields. Most useful for text, email, url, phone types.',
                'example' => 'Updated placeholder',
                'required' => false,
            ],
            'config' => [
                'description' => 'Question-specific configuration. Depends on the question type.',
                'example' => [
                    'min' => 1,
                    'max' => 5,
                ],
                'required' => false,
            ],
            'order_index' => [
                'description' => 'Order index for question positioning',
                'example' => 1,
                'required' => false,
            ],
        ];
    }
} 
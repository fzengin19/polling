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
        return [
            'type' => 'sometimes|required|string|max:50',
            'title' => 'sometimes|required|string|max:255',
            'is_required' => 'boolean',
            'help_text' => 'nullable|string|max:1000',
            'placeholder' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'order_index' => 'integer|min:0',
        ];
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
                'description' => 'Placeholder text for input fields',
                'example' => 'Updated placeholder',
                'required' => false,
            ],
            'config' => [
                'description' => 'Question configuration (validation, conditional logic, media references)',
                'example' => ['min_length' => 10, 'max_length' => 500],
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
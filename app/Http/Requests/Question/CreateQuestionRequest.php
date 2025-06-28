<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_id' => 'required|integer|min:1',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
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
            'page_id.required' => 'Page ID is required.',
            'page_id.min' => 'Page ID must be at least 1.',
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
            'page_id' => [
                'description' => 'ID of the survey page this question belongs to',
                'example' => 1,
                'required' => true,
            ],
            'type' => [
                'description' => 'Question type (text, multiple_choice, rating, etc.)',
                'example' => 'multiple_choice',
                'required' => true,
            ],
            'title' => [
                'description' => 'Question title',
                'example' => 'What is your favorite color?',
                'required' => true,
            ],
            'is_required' => [
                'description' => 'Whether the question is required',
                'example' => false,
                'required' => false,
            ],
            'help_text' => [
                'description' => 'Help text for the question',
                'example' => 'Please select the color you like most',
                'required' => false,
            ],
            'placeholder' => [
                'description' => 'Placeholder text for input fields',
                'example' => 'Enter your answer here',
                'required' => false,
            ],
            'config' => [
                'description' => 'Question configuration (validation, conditional logic, media references)',
                'example' => ['min_length' => 10, 'max_length' => 500],
                'required' => false,
            ],
            'order_index' => [
                'description' => 'Order index for question positioning',
                'example' => 0,
                'required' => false,
            ],
        ];
    }
} 
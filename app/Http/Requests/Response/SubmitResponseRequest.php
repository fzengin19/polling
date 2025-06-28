<?php

namespace App\Http\Requests\Response;

use Illuminate\Foundation\Http\FormRequest;

class SubmitResponseRequest extends FormRequest
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
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer|min:1',
            'answers.*.choice_id' => 'sometimes|integer|min:1',
            'answers.*.value' => 'sometimes|string|max:1000',
            'answers.*.order_index' => 'sometimes|integer|min:0',
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
            'answers.required' => 'Answers are required.',
            'answers.min' => 'At least one answer is required.',
            'answers.*.question_id.required' => 'Question ID is required for each answer.',
            'answers.*.question_id.min' => 'Question ID must be at least 1.',
            'answers.*.choice_id.min' => 'Choice ID must be at least 1.',
            'answers.*.value.max' => 'Answer value cannot exceed 1000 characters.',
            'answers.*.order_index.min' => 'Order index must be at least 0.',
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
            'answers' => [
                'description' => 'Array of answers to questions',
                'example' => [
                    [
                        'question_id' => 1,
                        'choice_id' => 2,
                        'value' => null,
                        'order_index' => 0
                    ],
                    [
                        'question_id' => 2,
                        'choice_id' => null,
                        'value' => 'My text answer',
                        'order_index' => 0
                    ]
                ],
                'required' => true,
            ],
        ];
    }
} 
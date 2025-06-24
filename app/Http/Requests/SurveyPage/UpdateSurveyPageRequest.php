<?php

namespace App\Http\Requests\SurveyPage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyPageRequest extends FormRequest
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
            'title' => 'sometimes|nullable|string|max:255',
            'order_index' => 'sometimes|integer|min:0',
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
            'title.max' => 'Page title cannot exceed 255 characters.',
            'order_index.min' => 'Order index must be at least 0.',
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
                'description' => 'Page title',
                'example' => 'Updated Personal Information',
                'required' => false,
            ],
            'order_index' => [
                'description' => 'Order index for page positioning',
                'example' => 1,
                'required' => false,
            ],
        ];
    }
} 
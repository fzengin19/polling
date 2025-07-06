<?php

namespace App\Http\Requests\Choice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChoiceRequest extends FormRequest
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
            'label' => 'sometimes|string|max:255',
            'value' => 'sometimes|string|max:255',
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
            'label.max' => 'Choice label cannot exceed 255 characters.',
            'value.max' => 'Choice value cannot exceed 255 characters.',
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
            'label' => [
                'description' => 'The new text to be displayed for this choice. Maximum 255 characters.',
                'example' => 'Updated Option 1',
                'required' => false,
            ],
            'value' => [
                'description' => 'The new value to be stored for this choice. Maximum 255 characters.',
                'example' => 'updated_option_1',
                'required' => false,
            ],
            'order_index' => [
                'description' => 'Order index for choice positioning',
                'example' => 1,
                'required' => false,
            ],
        ];
    }
} 
<?php

namespace App\Http\Requests\Choice;

use Illuminate\Foundation\Http\FormRequest;

class CreateChoiceRequest extends FormRequest
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
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'order_index' => 'integer|min:0',
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
            'label.required' => 'Choice label is required.',
            'label.max' => 'Choice label cannot exceed 255 characters.',
            'value.required' => 'Choice value is required.',
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
                'description' => 'Choice label (display text)',
                'example' => 'Red',
                'required' => true,
            ],
            'value' => [
                'description' => 'Choice value (stored value)',
                'example' => 'red',
                'required' => true,
            ],
            'order_index' => [
                'description' => 'Order index for choice positioning',
                'example' => 0,
                'required' => false,
            ],
        ];
    }
} 
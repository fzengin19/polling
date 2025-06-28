<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaMetadataRequest extends FormRequest
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
            'alt_text' => 'sometimes|string|max:255',
            'caption' => 'sometimes|string|max:500',
            'display_order' => 'sometimes|integer|min:0',
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
            'alt_text.max' => 'Alt text cannot exceed 255 characters.',
            'caption.max' => 'Caption cannot exceed 500 characters.',
            'display_order.min' => 'Display order must be at least 0.',
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
            'alt_text' => [
                'description' => 'Alternative text for the media',
                'example' => 'Updated alt text',
                'required' => false,
            ],
            'caption' => [
                'description' => 'Caption for the media',
                'example' => 'Updated caption',
                'required' => false,
            ],
            'display_order' => [
                'description' => 'Display order for media positioning',
                'example' => 1,
                'required' => false,
            ],
        ];
    }
} 
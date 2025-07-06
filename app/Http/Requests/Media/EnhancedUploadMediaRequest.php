<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class EnhancedUploadMediaRequest extends FormRequest
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
            'file' => 'required|file',
            'collection' => 'sometimes|string',
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
            'file.required' => 'File is required.',
            'file.file' => 'The uploaded file is not valid.',
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
            'file' => [
                'description' => 'Media file to upload.',
                'example' => 'image.jpg',
                'required' => true,
            ],
            'collection' => [
                'description' => 'Media collection name (optional, defaults to "default").',
                'example' => 'question-images',
                'required' => false,
            ],
        ];
    }
} 
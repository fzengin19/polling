<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
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
            'question_id' => 'required|integer|min:1',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:10240', // 10MB max
            'alt_text' => 'sometimes|string|max:255',
            'caption' => 'sometimes|string|max:500',
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
            'question_id.required' => 'Question ID is required.',
            'question_id.min' => 'Question ID must be at least 1.',
            'file.required' => 'File is required.',
            'file.file' => 'The uploaded file is not valid.',
            'file.mimes' => 'File must be one of: jpeg, png, jpg, gif, svg, pdf, doc, docx.',
            'file.max' => 'File size cannot exceed 10MB.',
            'alt_text.max' => 'Alt text cannot exceed 255 characters.',
            'caption.max' => 'Caption cannot exceed 500 characters.',
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
            'question_id' => [
                'description' => 'ID of the question to attach media to',
                'example' => 1,
                'required' => true,
            ],
            'file' => [
                'description' => 'Media file to upload (jpeg, png, jpg, gif, svg, pdf, doc, docx, max 10MB)',
                'example' => 'image.jpg',
                'required' => true,
            ],
            'alt_text' => [
                'description' => 'Alternative text for the media',
                'example' => 'A red circle',
                'required' => false,
            ],
            'caption' => [
                'description' => 'Caption for the media',
                'example' => 'This is a sample image',
                'required' => false,
            ],
        ];
    }
} 
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
            'model_type' => 'required|string|in:question,survey,survey-page,choice',
            'model_id' => 'required|integer|min:1',
            'collection_name' => 'required|string|max:255',
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
            'model_type.required' => 'Model type is required.',
            'model_type.in' => 'Invalid model type provided.',
            'model_id.required' => 'Model ID is required.',
            'collection_name.required' => 'Collection name is required.',
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
            'model_type' => [
                'description' => 'Type of the model to attach media to (e.g., "question", "survey", "survey-page", "choice").',
                'example' => 'question',
                'required' => true,
            ],
            'model_id' => [
                'description' => 'ID of the model to attach media to. Must be at least 1.',
                'example' => 1,
                'required' => true,
            ],
            'collection_name' => [
                'description' => 'Name of the collection to store the media in. Maximum 255 characters.',
                'example' => 'images',
                'required' => true,
            ],
            'file' => [
                'description' => 'Media file to upload. Allowed types: jpeg, png, jpg, gif, svg, pdf, doc, docx. Maximum size: 10MB.',
                'example' => null,
                'required' => true,
            ],
            'alt_text' => [
                'description' => 'Alternative text for the media. Maximum 255 characters.',
                'example' => 'A red circle',
                'required' => false,
            ],
            'caption' => [
                'description' => 'Caption for the media. Maximum 500 characters.',
                'example' => 'This is a sample image',
                'required' => false,
            ],
        ];
    }
} 
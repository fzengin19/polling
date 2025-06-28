<?php

namespace App\Http\Requests\SurveyPage;

use Illuminate\Foundation\Http\FormRequest;

class ReorderSurveyPagesRequest extends FormRequest
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
            'page_ids' => 'required|array',
            'page_ids.*' => 'integer|exists:survey_pages,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'page_ids.required' => 'Page IDs are required.',
            'page_ids.array' => 'Page IDs must be an array.',
            'page_ids.*.integer' => 'Each page ID must be an integer.',
            'page_ids.*.exists' => 'One or more page IDs do not exist.',
        ];
    }
} 
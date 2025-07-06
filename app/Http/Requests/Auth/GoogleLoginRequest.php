<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'google_access_token' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'google_access_token.required' => 'Google access token is required.',
            'google_access_token.max' => 'Google access token cannot exceed 1000 characters.',
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'google_access_token' => [
                'description' => 'The Access Token provided by Google after user authentication.',
                'example' => 'ya29.a0AfH6SMD_...',
            ],
        ];
    }
} 
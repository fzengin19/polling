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

    public function bodyParameters(): array
    {
        return [
            'google_access_token' => [
                'description' => 'Google OAuth access token obtained from frontend',
                'example' => 'ya29.a0AfB_byC...',
                'required' => true,
            ],
        ];
    }
} 
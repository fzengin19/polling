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
            'google_access_token' => 'required|string',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'google_access_token' => [
                'description' => 'Google OAuth access token obtained from frontend',
                'example' => 'ya29.a0AfB_byC...',
            ],
        ];
    }
} 
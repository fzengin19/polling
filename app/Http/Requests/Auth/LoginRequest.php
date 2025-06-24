<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'User\'s email address',
                'example' => 'user@example.com',
            ],
            'password' => [
                'description' => 'User\'s password',
                'example' => 'password123',
            ],
        ];
    }
} 
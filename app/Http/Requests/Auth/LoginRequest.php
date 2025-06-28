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
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'password.required' => 'Password is required.',
            'password.max' => 'Password cannot exceed 255 characters.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'User\'s email address',
                'example' => 'user@example.com',
                'required' => true,
            ],
            'password' => [
                'description' => 'User\'s password',
                'example' => 'password123',
                'required' => true,
            ],
        ];
    }
} 
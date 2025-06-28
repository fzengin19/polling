<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password cannot exceed 255 characters.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'User\'s full name',
                'example' => 'Fatih Yılmaz',
                'required' => true,
            ],
            'email' => [
                'description' => 'User\'s email address',
                'example' => 'fatih@example.com',
                'required' => true,
            ],
            'password' => [
                'description' => 'User\'s password (minimum 8 characters)',
                'example' => 'password123',
                'required' => true,
            ],
        ];
    }
} 
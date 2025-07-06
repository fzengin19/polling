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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
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

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The user\'s name.',
                'example' => 'John Doe',
            ],
            'email' => [
                'description' => 'The user\'s email address. Must be unique.',
                'example' => 'john.doe@example.com',
            ],
            'password' => [
                'description' => 'The user\'s password. Minimum 8 characters.',
                'example' => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'The password confirmation. Must match the password.',
                'example' => 'password123',
            ],
        ];
    }
} 
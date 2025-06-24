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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'User\'s full name',
                'example' => 'Fatih Yılmaz',
            ],
            'email' => [
                'description' => 'User\'s email address',
                'example' => 'fatih@example.com',
            ],
            'password' => [
                'description' => 'User\'s password',
                'example' => 'password123',
            ],
        ];
    }
} 
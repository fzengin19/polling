<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
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
            'role_name' => 'required|string|max:255',
            'user_id' => 'nullable|integer|min:1',
            'survey_id' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'role_name.required' => 'Role name is required.',
            'role_name.max' => 'Role name cannot exceed 255 characters.',
            'user_id.min' => 'User ID must be at least 1.',
            'survey_id.min' => 'Survey ID must be at least 1.',
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
            'role_name' => [
                'description' => 'Name of the role to assign',
                'example' => 'survey_editor',
                'required' => true,
            ],
            'user_id' => [
                'description' => 'ID of the user to assign the role to',
                'example' => 1,
                'required' => false,
            ],
            'survey_id' => [
                'description' => 'ID of the survey to assign the role for',
                'example' => 1,
                'required' => false,
            ],
        ];
    }
} 
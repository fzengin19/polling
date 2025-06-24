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
            'role_name' => 'required|string|exists:roles,name',
            'user_id' => 'nullable|integer|exists:users,id',
            'survey_id' => 'nullable|integer|exists:surveys,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'role_name.required' => 'Role name is required.',
            'role_name.exists' => 'The specified role does not exist.',
            'user_id.exists' => 'The specified user does not exist.',
            'survey_id.exists' => 'The specified survey does not exist.',
        ];
    }
} 
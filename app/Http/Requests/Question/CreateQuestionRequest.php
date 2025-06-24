<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_id' => 'required|integer',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'is_required' => 'boolean',
            'help_text' => 'nullable|string',
            'placeholder' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'order_index' => 'integer|min:0',
        ];
    }
} 
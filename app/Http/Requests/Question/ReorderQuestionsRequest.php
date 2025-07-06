<?php

declare(strict_types=1);

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;

class ReorderQuestionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Yetkilendirme controller'da yapılacak
        return true;
    }

    public function rules(): array
    {
        return [
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['integer'],
        ];
    }
} 
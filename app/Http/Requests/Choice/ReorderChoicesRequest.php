<?php

declare(strict_types=1);

namespace App\Http\Requests\Choice;

use Illuminate\Foundation\Http\FormRequest;

class ReorderChoicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Yetkilendirme controller'da yapılacak
        return true;
    }

    public function rules(): array
    {
        return [
            'choice_ids' => ['required', 'array'],
            'choice_ids.*' => ['integer'],
        ];
    }
} 
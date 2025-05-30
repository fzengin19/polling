<?php

namespace App\Http\Requests\PollVote;

use Illuminate\Foundation\Http\FormRequest;

class StorePollVoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'option_id' => ['required', 'exists:options,id'],
        ];
    }
}

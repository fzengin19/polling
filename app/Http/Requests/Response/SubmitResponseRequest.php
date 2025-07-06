<?php

namespace App\Http\Requests\Response;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Question;

class SubmitResponseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Policy-based authorization can be added here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.choice_id' => 'nullable|integer|exists:choices,id',
        ];

        // Gelen cevapları döngüye alarak her birine özel kural ata
        foreach ($this->input('answers', []) as $key => $answer) {
            if (empty($answer['question_id'])) {
                continue;
            }

            // Eğer choice_id doluysa, bu bir metin cevabı değildir, value validasyonunu atla
            if (!empty($answer['choice_id'])) {
                continue;
            }

            $question = Question::find($answer['question_id']);

            if (!$question) {
                continue;
            }

            $valueRules = ['nullable']; // Temel kural, string veya array olabilir

            switch ($question->type) {
                case 'email':
                    $valueRules = array_merge($valueRules, ['email', 'max:255']);
                    break;
                case 'url':
                    $valueRules = array_merge($valueRules, ['url', 'max:1000']);
                    break;
                case 'number':
                    $valueRules = array_merge($valueRules, ['numeric']);
                    if (isset($question->config['min'])) {
                        $valueRules[] = 'min:' . $question->config['min'];
                    }
                    if (isset($question->config['max'])) {
                        $valueRules[] = 'max:' . $question->config['max'];
                    }
                    break;
                case 'phone':
                    $valueRules = array_merge($valueRules, ['string', 'regex:/^[+\d\s-]+$/', 'max:20']);
                    break;
                case 'checkbox':
                    $valueRules = array_merge($valueRules, ['array', 'min:1']);
                    $rules["answers.{$key}.value.*"] = 'integer|exists:choices,id,question_id,' . $question->id;
                    break;
                case 'dropdown':
                    $valueRules = array_merge($valueRules, ['integer', 'exists:choices,id,question_id,' . $question->id]);
                    break;
                case 'linear_scale':
                    $min = $question->config['min'] ?? 1;
                    $max = $question->config['max'] ?? 5;
                    $valueRules = array_merge($valueRules, ['integer', "between:{$min},{$max}"]);
                    break;
                case 'date':
                    $valueRules = array_merge($valueRules, ['date']);
                    break;
                case 'time':
                    $valueRules = array_merge($valueRules, ['date_format:H:i:s']);
                    break;
                case 'boolean':
                    $valueRules = array_merge($valueRules, ['boolean']);
                    break;
                default:
                    $valueRules = array_merge($valueRules, ['string', 'max:1000']);
            }

            if ($question->is_required) {
                // 'nullable' kuralını 'required' ile değiştir
                $valueRules = array_filter($valueRules, fn($rule) => $rule !== 'nullable');
                array_unshift($valueRules, 'required');
            }

            $rules["answers.{$key}.value"] = $valueRules;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'answers.required' => 'Answers are required.',
            'answers.min' => 'At least one answer is required.',
            'answers.*.question_id.required' => 'Question ID is required for each answer.',
            'answers.*.question_id.exists' => 'The specified question does not exist.',
            'answers.*.value.max' => 'Answer value cannot exceed 1000 characters.',
            'answers.*.choice_id.exists' => 'The specified choice does not exist.',
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
            'answers' => [
                'description' => 'Array of answers to questions. At least one answer is required.',
                'example' => [
                    [
                        'question_id' => 1,
                        'choice_id' => 2,
                        'value' => null
                    ],
                    [
                        'question_id' => 2,
                        'choice_id' => null,
                        'value' => 'My text answer'
                    ]
                ],
                'required' => true,
            ],
            'answers.*.question_id' => [
                'description' => 'ID of the question being answered. Must exist in the questions table.',
                'example' => 1,
                'required' => true,
            ],
            'answers.*.value' => [
                'description' => 'Value of the answer. Validation rules depend on the question type.',
                'example' => 'My dynamic answer',
                'required' => false,
            ],
            'answers.*.choice_id' => [
                'description' => 'ID of the selected choice (for multiple choice questions). Must exist in the choices table.',
                'example' => 2,
                'required' => false,
            ],
        ];
    }
} 
<?php

namespace App\Repositories\Eloquent;

use App\Models\Question;
use App\Repositories\Abstract\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function getBySurveyPage(int $surveyPageId): Collection
    {
        return Question::where('page_id', $surveyPageId)->orderBy('order_index')->get();
    }

    public function getActiveBySurveyPage(int $surveyPageId): Collection
    {
        return Question::where('page_id', $surveyPageId)
            ->orderBy('order_index')
            ->get();
    }

    public function findById(int $id): ?Question
    {
        return Question::find($id);
    }

    public function create(array $data): Question
    {
        return Question::create($data);
    }

    public function update(Question $question, array $data): Question
    {
        $question->update($data);
        return $question;
    }

    public function delete(Question $question): bool
    {
        return $question->delete();
    }

    public function reorder(int $surveyPageId, array $questionIds): bool
    {
        foreach ($questionIds as $order => $id) {
            Question::where('id', $id)
                ->where('page_id', $surveyPageId)
                ->update(['order_index' => $order]);
        }
        return true;
    }

    public function getByType(int $surveyPageId, string $type): Collection
    {
        return Question::where('page_id', $surveyPageId)
            ->where('type', $type)
            ->orderBy('order_index')
            ->get();
    }
} 
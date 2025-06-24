<?php

namespace App\Repositories\Abstract;

use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;

interface QuestionRepositoryInterface
{
    /**
     * Get all questions for a survey page.
     */
    public function getBySurveyPage(int $surveyPageId): Collection;

    /**
     * Get active questions for a survey page.
     */
    public function getActiveBySurveyPage(int $surveyPageId): Collection;

    /**
     * Get question by ID.
     */
    public function findById(int $id): ?Question;

    /**
     * Create a new question.
     */
    public function create(array $data): Question;

    /**
     * Update an existing question.
     */
    public function update(Question $question, array $data): Question;

    /**
     * Delete a question.
     */
    public function delete(Question $question): bool;

    /**
     * Reorder questions within a survey page.
     */
    public function reorder(int $surveyPageId, array $questionIds): bool;

    /**
     * Get questions by type for a survey page.
     */
    public function getByType(int $surveyPageId, string $type): Collection;
} 
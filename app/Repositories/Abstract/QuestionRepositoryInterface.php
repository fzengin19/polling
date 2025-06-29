<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;

interface QuestionRepositoryInterface extends BaseRepositoryInterface
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
     * Reorder questions within a survey page.
     */
    public function reorder(int $surveyPageId, array $questionIds): bool;

    /**
     * Get questions by type for a survey page.
     */
    public function getByType(int $surveyPageId, string $type): Collection;
} 
<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface SurveyPageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all pages for a survey
     */
    public function findBySurvey(int $surveyId): Collection;

    /**
     * Get pages for a survey ordered by order_index
     */
    public function getOrderedPages(int $surveyId): Collection;

    /**
     * Get next order index for a survey
     */
    public function getNextOrderIndex(int $surveyId): int;

    /**
     * Reorder pages
     */
    public function reorder(array $pageIds): void;
} 
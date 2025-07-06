<?php

namespace App\Services\Concrete;

use App\Dtos\ResponseDto;
use App\Dtos\SubmitResponseDto;
use App\Repositories\Abstract\ResponseRepositoryInterface;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Responses\Abstract\ResourceMapInterface;
use App\Responses\ServiceResponse;
use App\Services\Abstract\ResponseServiceInterface;
use Illuminate\Support\Facades\Auth;

class ResponseService implements ResponseServiceInterface
{
    public function __construct(
        protected ResponseRepositoryInterface $responseRepository,
        protected SurveyRepositoryInterface $surveyRepository,
        protected ResourceMapInterface $resourceMap
    ) {}

    public function create(ResponseDto $dto): ServiceResponse
    {
        $survey = $this->surveyRepository->find($dto->surveyId);

        if (!$survey || $survey->status !== 'active') {
            return ServiceResponse::error('Survey is not active or not found.', null, 422);
        }

        if ($survey->expires_at && $survey->expires_at->isPast()) {
            return ServiceResponse::error('Survey has expired.', null, 422);
        }
        
        if ($survey->max_responses) {
            $responseCount = $this->responseRepository->countBySurvey($survey->id);
            if ($responseCount >= $survey->max_responses) {
                return ServiceResponse::error('Survey has reached its maximum number of responses.', null, 422);
            }
        }
        
        $data = $dto->toDatabaseArray();
        $data['user_id'] = Auth::id();

        $response = $this->responseRepository->create($data);
        return ServiceResponse::created($response, 'Response session started.');
    }

    public function find(int $id): ServiceResponse
    {
        $response = $this->responseRepository->find($id);
        if (!$response) {
            return ServiceResponse::notFound('Response not found.');
        }
        return ServiceResponse::success($response);
    }

    public function submit(SubmitResponseDto $dto): ServiceResponse
    {
        $response = $this->responseRepository->find($dto->responseId);
        if (!$response) {
            return ServiceResponse::notFound('Response not found.');
        }

        // Business logic for saving answers would go here.
        
        $this->responseRepository->update($dto->responseId, [
            'submitted_at' => now(),
            'is_complete' => true,
        ]);

        return ServiceResponse::success(['is_complete' => true], 'Response submitted successfully.');
    }

    public function getStatistics(int $surveyId): ServiceResponse
    {
        $stats = $this->responseRepository->getStatistics($surveyId);
        return ServiceResponse::success($stats);
    }
} 
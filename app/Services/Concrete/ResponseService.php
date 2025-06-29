<?php

namespace App\Services\Concrete;

use App\Dtos\ResponseDto;
use App\Dtos\SubmitResponseDto;
use App\Repositories\Abstract\AnswerRepositoryInterface;
use App\Repositories\Abstract\ResponseRepositoryInterface;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Responses\ServiceResponse;
use App\Responses\Concrete\ApiResourceMap;
use App\Services\Abstract\ResponseServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResponseService implements ResponseServiceInterface
{
    public function __construct(
        protected ApiResourceMap $resourceMap,
        protected SurveyRepositoryInterface $surveyRepository,
        protected ResponseRepositoryInterface $responseRepository,
        protected AnswerRepositoryInterface $answerRepository
    ) {}

    public function create(ResponseDto $dto): ServiceResponse
    {
        try {
            DB::beginTransaction();

            $survey = $this->surveyRepository->find($dto->survey_id);
            if (!$survey) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Survey not found'], $this->resourceMap, 404);
            }

            if ($survey->status !== 'active') {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Survey is not active'], $this->resourceMap, 422);
            }

            if ($survey->expires_at && Carbon::parse($survey->expires_at)->isPast()) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Survey has expired'], $this->resourceMap, 422);
            }

            if ($survey->max_responses) {
                $responseCount = $this->responseRepository->getCountBySurvey($survey->id);
                if ($responseCount >= $survey->max_responses) {
                    DB::rollBack();
                    return new ServiceResponse(['message' => 'Maximum responses limit reached'], $this->resourceMap, 422);
                }
            }

            $data = $dto->toDatabaseArray();
            $data['user_id'] = Auth::id() ?? null;
            $data['started_at'] = now();
            $data['is_complete'] = false;

            $response = $this->responseRepository->create($data);

            DB::commit();
            return new ServiceResponse($response, $this->resourceMap, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function find(int $id): ServiceResponse
    {
        try {
            $response = $this->responseRepository->find($id);
            if (!$response) {
                return new ServiceResponse(['message' => 'Response not found'], $this->resourceMap, 404);
            }
            // Eager load relationships if needed by the resource
            $response->load(['answers.choice', 'answers.question']);
            return new ServiceResponse($response, $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function submit(SubmitResponseDto $dto): ServiceResponse
    {
        try {
            DB::beginTransaction();

            $response = $this->responseRepository->find($dto->responseId);
            if (!$response) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Response not found'], $this->resourceMap, 404);
            }

            if ($response->is_complete) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Response is already complete'], $this->resourceMap, 422);
            }

            foreach ($dto->answers as $answerData) {
                $answerData['response_id'] = $response->id;
                $this->answerRepository->create($answerData);
            }

            $this->responseRepository->update($response->id, [
                'submitted_at' => now(),
                'is_complete' => true,
            ]);

            DB::commit();
            return new ServiceResponse(['message' => 'Response submitted successfully'], $this->resourceMap, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getStatistics(int $surveyId): ServiceResponse
    {
        try {
            $survey = $this->surveyRepository->find($surveyId);
            if (!$survey) {
                return new ServiceResponse(['message' => 'Survey not found'], $this->resourceMap, 404);
            }

            $totalResponses = $this->responseRepository->getCountBySurvey($surveyId);
            $completedResponses = $this->responseRepository->getCompletedCountBySurvey($surveyId);
            $incompleteResponses = $totalResponses - $completedResponses;

            return new ServiceResponse([
                'total_responses' => $totalResponses,
                'completed_responses' => $completedResponses,
                'incomplete_responses' => $incompleteResponses,
            ], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }
} 
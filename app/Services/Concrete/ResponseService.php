<?php

namespace App\Services\Concrete;

use App\Dtos\ResponseDto;
use App\Dtos\SubmitResponseDto;
use App\Models\Response;
use App\Models\Survey;
use App\Models\Answer;
use App\Responses\ServiceResponse;
use App\Responses\Concrete\ApiResourceMap;
use App\Services\Abstract\ResponseServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResponseService implements ResponseServiceInterface
{
    public function __construct(
        protected ApiResourceMap $resourceMap
    ) {}

    public function create(ResponseDto $dto): ServiceResponse
    {
        try {
            DB::beginTransaction();

            $survey = Survey::find($dto->survey_id);
            if (!$survey) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Survey not found'], $this->resourceMap, 404);
            }

            // Check if survey is active
            if ($survey->status !== 'active') {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Survey is not active'], $this->resourceMap, 422);
            }

            // Check if survey is expired
            if ($survey->expires_at && Carbon::parse($survey->expires_at)->isPast()) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Survey has expired'], $this->resourceMap, 422);
            }

            // Check if max responses limit is reached
            if ($survey->max_responses) {
                $responseCount = Response::where('survey_id', $survey->id)->count();
                if ($responseCount >= $survey->max_responses) {
                    DB::rollBack();
                    return new ServiceResponse(['message' => 'Maximum responses limit reached'], $this->resourceMap, 422);
                }
            }

            $response = Response::create([
                'survey_id' => $dto->survey_id,
                'user_id' => $dto->user_id,
                'started_at' => now(),
                'metadata' => $dto->metadata,
                'is_complete' => false,
            ]);

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
            $response = Response::with(['answers.choice', 'answers.question'])->find($id);
            if (!$response) {
                return new ServiceResponse(['message' => 'Response not found'], $this->resourceMap, 404);
            }
            return new ServiceResponse($response, $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function submit(SubmitResponseDto $dto): ServiceResponse
    {
        try {
            DB::beginTransaction();

            $response = Response::find($dto->responseId);
            if (!$response) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Response not found'], $this->resourceMap, 404);
            }

            // Check if response is already complete
            if ($response->is_complete) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Response is already complete'], $this->resourceMap, 422);
            }

            // Save answers
            foreach ($dto->answers as $answerData) {
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $answerData['question_id'],
                    'choice_id' => $answerData['choice_id'] ?? null,
                    'value' => $answerData['value'] ?? null,
                    'order_index' => $answerData['order_index'] ?? 0,
                ]);
            }

            // Mark response as complete
            $response->update([
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
            $survey = Survey::find($surveyId);
            if (!$survey) {
                return new ServiceResponse(['message' => 'Survey not found'], $this->resourceMap, 404);
            }

            $totalResponses = Response::where('survey_id', $surveyId)->count();
            $completedResponses = Response::where('survey_id', $surveyId)->where('is_complete', true)->count();
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
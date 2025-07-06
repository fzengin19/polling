<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Response\CreateResponseRequest;
use App\Http\Requests\Response\SubmitResponseRequest;
use App\Services\Abstract\ResponseServiceInterface;
use App\Dtos\ResponseDto;
use App\Dtos\SubmitResponseDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Response Management
 *
 * APIs for managing survey responses and submissions
 */
class ResponseController extends Controller
{
    public function __construct(
        private ResponseServiceInterface $responseService
    ) {}

    /**
     * Start Response
     *
     * Start a new response session for a survey.
     * @responseFile status=201 storage/app/private/scribe/responses/responses.show.json
     */
    public function store(CreateResponseRequest $request): JsonResponse
    {
        $dto = ResponseDto::fromArray($request->validated());
        $result = $this->responseService->create($dto);
        return $result->toResponse();
    }

    /**
     * Get Response
     *
     * Get the details of a specific response.
     * @urlParam id required The ID of the response. Example: 1
     * @responseFile storage/app/private/scribe/responses/responses.show.json
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->responseService->find($id);
        return $result->toResponse();
    }

    /**
     * Submit Response
     *
     * Submit answers for a response to complete the survey.
     * @urlParam id required The ID of the response. Example: 1
     * @responseFile storage/app/private/scribe/responses/responses.show.json
     */
    public function submit(SubmitResponseRequest $request, int $id): JsonResponse
    {
        $dto = new SubmitResponseDto(
            responseId: $id,
            answers: $request->validated('answers'),
        );

        $result = $this->responseService->submit($dto);
        return $result->toResponse();
    }
} 
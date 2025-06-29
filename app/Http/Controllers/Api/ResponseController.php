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

class ResponseController extends Controller
{
    public function __construct(
        private ResponseServiceInterface $responseService
    ) {}

    public function store(CreateResponseRequest $request): JsonResponse
    {
        $dto = ResponseDto::fromArray($request->validated());

        $result = $this->responseService->create($dto);
        return $result->toResponse();
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->responseService->find($id);
        return $result->toResponse();
    }

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
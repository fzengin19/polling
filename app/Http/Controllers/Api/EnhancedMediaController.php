<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Http\Requests\Media\UpdateMediaMetadataRequest;
use App\Models\Choice;
use App\Models\Question;
use App\Models\Survey;
use App\Models\SurveyPage;
use App\Services\Abstract\MediaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnhancedMediaController extends Controller
{
    public function __construct(
        private MediaServiceInterface $mediaService
    ) {}

    /**
     * Upload media for a specific model
     */
    public function uploadMedia(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $model = $this->getModel($modelType, $modelId);
        
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $collection = $request->input('collection', 'default');
        $file = $request->file('file');

        if (!$file) {
            return response()->json(['message' => 'No file provided'], 400);
        }

        $result = $this->mediaService->uploadMediaForModel($model, $file, $collection);
        return $result->toResponse();
    }

    /**
     * Get media for a specific model
     */
    public function getMedia(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $model = $this->getModel($modelType, $modelId);
        
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $collection = $request->input('collection');
        
        $result = $this->mediaService->getMedia($model, $collection);
        return $result->toResponse();
    }

    /**
     * Update media metadata
     */
    public function updateMediaMetadata(UpdateMediaMetadataRequest $request, int $mediaId): JsonResponse
    {
        $result = $this->mediaService->updateMediaMetadata($mediaId, $request->validated());
        return $result->toResponse();
    }

    /**
     * Delete media
     */
    public function deleteMedia(int $mediaId): JsonResponse
    {
        $result = $this->mediaService->deleteMedia($mediaId);
        return $result->toResponse();
    }

    /**
     * Get available collections for a model type
     */
    public function getCollections(string $modelType): JsonResponse
    {
        $result = $this->mediaService->getCollections($modelType);
        return $result->toResponse();
    }

    /**
     * Get model instance by type and ID
     */
    private function getModel(string $modelType, int $modelId)
    {
        return match ($modelType) {
            'survey' => Survey::find($modelId),
            'survey-page' => SurveyPage::find($modelId),
            'question' => Question::find($modelId),
            'choice' => Choice::find($modelId),
            default => null
        };
    }
} 
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

        try {
            $media = $this->mediaService->uploadMediaForModel($model, $file, $collection);
            
            return response()->json([
                'message' => 'Media uploaded successfully',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'url' => $media->getUrl(),
                    'collection' => $collection,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to upload media: ' . $e->getMessage()], 500);
        }
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
        
        try {
            $media = $this->mediaService->getMedia($model, $collection);
            
            return response()->json([
                'message' => 'Media retrieved successfully',
                'data' => $media->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'file_name' => $item->file_name,
                        'mime_type' => $item->mime_type,
                        'size' => $item->size,
                        'url' => $item->getUrl(),
                        'collection' => $item->collection_name,
                        'custom_properties' => $item->custom_properties,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve media: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update media metadata
     */
    public function updateMediaMetadata(UpdateMediaMetadataRequest $request, int $mediaId): JsonResponse
    {
        try {
            $media = $this->mediaService->updateMediaMetadata($mediaId, $request->validated());
            
            return response()->json([
                'message' => 'Media metadata updated successfully',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'custom_properties' => $media->custom_properties,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update media metadata: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete media
     */
    public function deleteMedia(int $mediaId): JsonResponse
    {
        try {
            $this->mediaService->deleteMedia($mediaId);
            
            return response()->json(['message' => 'Media deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete media: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get available collections for a model type
     */
    public function getCollections(string $modelType): JsonResponse
    {
        $collections = match ($modelType) {
            'survey' => ['survey-banners', 'survey-logos', 'survey-attachments'],
            'survey-page' => ['page-images', 'page-backgrounds'],
            'question' => ['question-images', 'question-videos', 'question-documents'],
            'choice' => ['choice-images', 'choice-icons'],
            default => []
        };

        return response()->json([
            'message' => 'Collections retrieved successfully',
            'data' => $collections
        ]);
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
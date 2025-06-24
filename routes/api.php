<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\EnhancedMediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('google', [AuthController::class, 'googleLogin']);
});

Route::prefix('templates')->group(function () {
    Route::get('/', [TemplateController::class, 'index']);
    Route::get('/public', [TemplateController::class, 'publicTemplates']);
    Route::get('/my', [TemplateController::class, 'myTemplates'])->middleware('auth:sanctum');
    Route::post('/', [TemplateController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/{id}', [TemplateController::class, 'show']);
    Route::put('/{id}', [TemplateController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [TemplateController::class, 'destroy'])->middleware('auth:sanctum');
    Route::post('/{id}/fork', [TemplateController::class, 'fork'])->middleware('auth:sanctum');
    
    // Template Version Management
    Route::get('/{id}/versions', [TemplateController::class, 'versions']);
    Route::post('/{id}/versions', [TemplateController::class, 'createVersion'])->middleware('auth:sanctum');
    Route::post('/{id}/versions/{versionId}/restore', [TemplateController::class, 'restoreVersion'])->middleware('auth:sanctum');
});

Route::prefix('surveys')->group(function () {
    Route::get('/', [SurveyController::class, 'index']);
    Route::get('/active', [SurveyController::class, 'activeSurveys']);
    Route::get('/my', [SurveyController::class, 'mySurveys'])->middleware('auth:sanctum');
    Route::get('/status/{status}', [SurveyController::class, 'byStatus']);
    Route::get('/template/{templateId}', [SurveyController::class, 'byTemplate']);
    Route::post('/', [SurveyController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/{id}', [SurveyController::class, 'show']);
    Route::put('/{id}', [SurveyController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [SurveyController::class, 'destroy'])->middleware('auth:sanctum');
    Route::post('/{id}/publish', [SurveyController::class, 'publish'])->middleware('auth:sanctum');
    Route::post('/{id}/archive', [SurveyController::class, 'archive'])->middleware('auth:sanctum');
    Route::post('/{id}/duplicate', [SurveyController::class, 'duplicate'])->middleware('auth:sanctum');
    
    // Survey Page Management
    Route::get('/{surveyId}/pages', [SurveyController::class, 'pages']);
    Route::post('/pages', [SurveyController::class, 'storePage'])->middleware('auth:sanctum');
    Route::get('/pages/{id}', [SurveyController::class, 'showPage']);
    Route::put('/pages/{id}', [SurveyController::class, 'updatePage'])->middleware('auth:sanctum');
    Route::delete('/pages/{id}', [SurveyController::class, 'destroyPage'])->middleware('auth:sanctum');
    Route::post('/{surveyId}/pages/reorder', [SurveyController::class, 'reorderPages'])->middleware('auth:sanctum');
});

// Question management
Route::prefix('survey-pages/{surveyPageId}')->group(function () {
    Route::get('questions', [\App\Http\Controllers\Api\QuestionController::class, 'index']);
    Route::get('questions/type/{type}', [\App\Http\Controllers\Api\QuestionController::class, 'byType']);
    Route::post('questions/reorder', [\App\Http\Controllers\Api\QuestionController::class, 'reorder']);
    Route::post('questions', [\App\Http\Controllers\Api\QuestionController::class, 'store']);
});
Route::get('questions/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'show']);
Route::put('questions/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'update']);
Route::delete('questions/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'destroy']);

// Role management
Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/assign', [RoleController::class, 'assign']);
    Route::post('/remove', [RoleController::class, 'remove']);
    Route::get('/users/{userId}', [RoleController::class, 'userRoles']);
    Route::get('/surveys/{surveyId}', [RoleController::class, 'surveyRoles']);
    Route::get('/users/{userId}/has/{roleName}', [RoleController::class, 'userHasRole']);
    Route::get('/surveys/{surveyId}/has/{roleName}', [RoleController::class, 'surveyHasRole']);
});

// Media routes
Route::prefix('media')->middleware('auth:sanctum')->group(function () {
    Route::post('/upload', [MediaController::class, 'upload']);
    Route::delete('/{mediaId}', [MediaController::class, 'delete']);
    Route::put('/{mediaId}/metadata', [MediaController::class, 'updateMetadata']);
});

Route::get('/questions/{questionId}/media', [MediaController::class, 'getQuestionMedia']);

// Enhanced Media routes for all models
Route::prefix('enhanced-media')->middleware('auth:sanctum')->group(function () {
    Route::post('/{modelType}/{modelId}/upload', [EnhancedMediaController::class, 'uploadMedia']);
    Route::get('/{modelType}/{modelId}/media', [EnhancedMediaController::class, 'getMedia']);
    Route::put('/{mediaId}/metadata', [EnhancedMediaController::class, 'updateMediaMetadata']);
    Route::delete('/{mediaId}', [EnhancedMediaController::class, 'deleteMedia']);
    Route::get('/{modelType}/collections', [EnhancedMediaController::class, 'getCollections']);
});

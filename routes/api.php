<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ChoiceController;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('google', [AuthController::class, 'googleLogin']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [UserController::class, 'update']);
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
    Route::post('/{id}/versions/{versionId}/restore', [TemplateController::class, 'restoreVersion']);
});

Route::prefix('surveys')->group(function () {
    Route::get('/', [SurveyController::class, 'index']);
    Route::get('/active', [SurveyController::class, 'activeSurveys']);
    Route::get('/my', [SurveyController::class, 'mySurveys'])->middleware('auth:sanctum');
    Route::get('/status/{status}', [SurveyController::class, 'byStatus']);
    Route::get('/template/{templateId}', [SurveyController::class, 'byTemplate']);
    Route::post('/', [SurveyController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/{id}', [SurveyController::class, 'show'])->middleware('auth:sanctum');
    Route::put('/{id}', [SurveyController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [SurveyController::class, 'destroy'])->middleware('auth:sanctum');
    Route::post('/{id}/publish', [SurveyController::class, 'publish'])->middleware('auth:sanctum');
    Route::post('/{id}/archive', [SurveyController::class, 'archive'])->middleware('auth:sanctum');
    Route::post('/{id}/duplicate', [SurveyController::class, 'duplicate'])->middleware('auth:sanctum');
    
    // Survey Page Management
    Route::get('/{surveyId}/pages', [SurveyController::class, 'pages'])->middleware('auth:sanctum');
    Route::post('/pages', [SurveyController::class, 'storePage'])->middleware('auth:sanctum');
    Route::get('/pages/{id}', [SurveyController::class, 'showPage'])->middleware('auth:sanctum');
    Route::put('/pages/{id}', [SurveyController::class, 'updatePage'])->middleware('auth:sanctum');
    Route::delete('/pages/{id}', [SurveyController::class, 'destroyPage'])->middleware('auth:sanctum');
    Route::post('/{surveyId}/pages/reorder', [SurveyController::class, 'reorderPages'])->middleware('auth:sanctum');
    
    // Survey Response Statistics
    Route::get('/{id}/responses', [SurveyController::class, 'responseStatistics']);
});

// Question management
Route::prefix('survey-pages/{pageId}')->middleware('auth:sanctum')->group(function () {
    Route::get('questions', [\App\Http\Controllers\Api\QuestionController::class, 'index']);
    Route::post('questions/reorder', [\App\Http\Controllers\Api\QuestionController::class, 'reorder']);
    Route::post('questions', [\App\Http\Controllers\Api\QuestionController::class, 'store']);
});
Route::get('questions/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'show'])->middleware('auth:sanctum');
Route::put('questions/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'update'])->middleware('auth:sanctum');
Route::delete('questions/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'destroy'])->middleware('auth:sanctum');

// Choice management
Route::apiResource('choices', ChoiceController::class)->except(['index', 'store'])->middleware('auth:sanctum');

// Question choices
Route::prefix('questions/{questionId}')->middleware('auth:sanctum')->group(function () {
    Route::get('choices', [ChoiceController::class, 'index']);
    Route::post('choices', [ChoiceController::class, 'store']);
    Route::post('choices/reorder', [ChoiceController::class, 'reorder']);
});

// Response management
Route::prefix('responses')->group(function () {
    Route::post('/', [ResponseController::class, 'store']);
    Route::get('/{id}', [ResponseController::class, 'show']);
    Route::post('/{id}/submit', [ResponseController::class, 'submit']);
});

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
    Route::delete('/{mediaId}', [MediaController::class, 'destroy']);
    Route::put('/{mediaId}/metadata', [MediaController::class, 'updateMetadata']);
});

Route::get('/questions/{questionId}/media', [MediaController::class, 'getQuestionMedia']);

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\SurveyController;
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

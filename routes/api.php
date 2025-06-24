<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TemplateController;
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
});

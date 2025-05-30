<?php

use App\Http\Controllers\Api\OptionController;
use App\Http\Controllers\Api\PollController;
use App\Http\Controllers\Api\PollVoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('polls', PollController::class);
Route::apiResource('options', OptionController::class);
Route::apiResource('poll-votes', PollVoteController::class);
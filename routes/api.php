<?php

use App\Http\Controllers\Api\V1\LeaderboardController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::post('/users', [UserController::class, 'createUser']);
    Route::post('/users/{userId}/score', [UserController::class, 'addScoreToUser']);
    Route::get('/leaderboard/top', [LeaderboardController::class, 'getTopUsers']);
    Route::get('/leaderboard/rank/{userId}', [LeaderboardController::class, 'getUserRank']);
});

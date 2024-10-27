<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'create']);
Route::post('/users/{userId}/score', [UserController::class, 'addScore']);
Route::get('/leaderboard/top', [UserController::class, 'getTopUsers']);
Route::get('/leaderboard/rank/{userId}', [UserController::class, 'getUserRank']);

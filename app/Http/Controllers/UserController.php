<?php

namespace App\Http\Controllers;

use App\Models\ScoreLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/|unique:users,username',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Некорректные параметры запроса',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create(['username' => $request->username]);

        return response()->json([
            'status' => 'Пользователь успешно создан',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
            ]
        ], 201);
    }

    public function addScore(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required|integer|between:1,10000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($userId);

        ScoreLog::create([
            'user_id' => $user->id,
            'points' => $request->points,
        ]);

        $user->increment('score', $request->points);

        return response()->json([
            'status' => 'success',
            'message' => 'Очки успешно добавлены',
            'user_id' => $userId,
            'new_total_score' => $user->score
        ], 200);
    }
}

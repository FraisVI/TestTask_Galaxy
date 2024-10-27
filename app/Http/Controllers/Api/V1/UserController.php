<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Некорректные параметры запроса',
                'errors' => $validator->errors(),
            ], 400); // 400 не удалось обработать инструкции содержимого
        }

        if (User::where('username', $request->username)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Пользователь с таким именем уже существует',
            ], 409); // 409 конфликт запроса
        }

        try {
            $user = User::create(['username' => $request->username]);

            return response()->json([
                'status' => 'Пользователь успешно создан',
                'user' => [
                    'id' => (int) $user->id,
                    'username' => $user->username,
                ],
            ], 201); // 201 Создан
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500); // 500 внутренняя ошибка сервера
        }
    }

    public function addScore(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required|integer|between:1,10000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Некорректные параметры запроса',
            ], 400);
        }

        try {
            $user = User::findOrFail($userId);

            ScoreLog::create([
                'user_id' => (int) $user->id,
                'points' => $request->points,
            ]);

            $user->increment('score', $request->points);

            return response()->json([
                'status' => 'success',
                'message' => 'Очки успешно добавлены',
                'user_id' => (int) $userId,
                'new_total_score' => $user->score,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'Некорректные параметры запроса',
                'error' => $e->getMessage(),
            ], 400); // 400 не удалось обработать инструкции содержимого
        }
    }
}

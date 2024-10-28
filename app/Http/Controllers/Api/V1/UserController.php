<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function createUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:3,50|regex:/^[a-zA-Z0-9_]+$/',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'Status' => 'Некорректные параметры запроса',
                'Errors' => $validator->messages(),
            ], 400); // 400 не удалось обработать инструкции содержимого
        } elseif (User::where('username', $request->username)->exists()) {
            return response()->json([
                'Status' => 'Conflict',
                'Message' => 'Пользователь с таким именем уже существует',
            ], 409); // 409 конфликт запроса
        }
        $user = $this->userService->createUser($request->only('username'));

        return response()->json([
            'Status' => 'Created',
            'Message' => 'Пользователь успешно создан',
            'user' => [
                'id' => (int) $user->id,
                'username' => $user->username,
            ],
        ], 201); // 201 успешное создание
    }

    public function addScoreToUser(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required|integer|between:1,10000',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'Status' => 'Некорректные параметры запроса',
                'Errors' => $validator->errors(),
            ], 400);
        }
        $scoreLog = $this->userService->addScore($userId, $request->only('points'));

        if (! $scoreLog) {
            return response()->json([
                'Status' => 'Error',
                'Message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'scoreLog' => $scoreLog,
        ], 200);
    }
}

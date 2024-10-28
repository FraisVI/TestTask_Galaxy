<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
                'Errors' => $validator->messages()->first(), //не задан шаблон для более точного описания ошибки
            ], 400);
        } elseif ($this->userService->checkDuplicateUser($request->username)) {
            return response()->json([
                'Errors' => 'Пользователь с таким именем уже существует',
            ], 409);
        }
        $user = $this->userService->createUser($request->only('username'));

        return response()->json([
            'user' => [
                'id' => (int) $user->id,
                'username' => $user->username,
            ],
        ], 201);
    }

    public function addScoreToUser(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required|integer|between:1,10000',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'Errors' => 'Некорректные параметры запроса',
            ], 400);
        }
        $scoreLog = $this->userService->addScore($userId, $request->points);

        if (! $scoreLog) {
            return response()->json([
                'Errors' => 'Пользователь не найден',
            ], 404);
        }

        return response()->json([
            'user_id' => (int) $userId,
            'new_total_score' => $scoreLog,
        ], 200);
    }
}

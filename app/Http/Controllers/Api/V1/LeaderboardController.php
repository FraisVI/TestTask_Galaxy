<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Providers\LeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaderboardController extends Controller
{
    protected LeaderboardService $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    public function getTopUsers(Request $request): JsonResponse
    {
        $period = $request->input('period', 'day');
        $this->validatePeriod($period);
        $topUsers = $this->leaderboardService->getTopUsers($period);

        return response()->json($topUsers);
    }

    public function getUserRank(Request $request, $userId): JsonResponse
    {
        $period = $request->input('period', 'day');
        $rank = $this->leaderboardService->getUserRank($userId, $period);

        return response()->json([
            'user_id' => $userId,
            'rank' => $rank,
        ]);
    }
    private function validatePeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'required|string|in:day,week,month',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Некорректные параметры запроса',
                'errors' => $validator->errors(),
            ], 400); // 400 не удалось обработать инструкции содержимого
        }
    }
}

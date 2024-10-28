<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ScoreLog;
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
        $validationResponse = $this->validatePeriod($period);
        if ($validationResponse) {
            return $validationResponse;
        }

        $topUsers = $this->leaderboardService->getTopUsers($period);

        return response()->json([
            'period' => $period,
            'top' => $topUsers,
        ]);
    }

    public function getUserRank(Request $request, $userId): JsonResponse
    {
        $period = $request->input('period', 'day');
        $validationResponse = $this->validatePeriod($period);
        if ($validationResponse) {
            return $validationResponse;
        }

        if (! ScoreLog::find($userId)) {
            return response()->json([
                'Status' => 'Not Found',
                'Message' => 'Пользователь не найден',
            ], 404);
        }
        $rank = $this->leaderboardService->getUserRank($userId, $period);

        return response()->json([
            'user_id' => $userId,
            'rank' => $rank,
        ]);
    }

    private function validatePeriod($period): ?JsonResponse
    {
        $validator = Validator::make(['period' => $period], [
            'period' => 'required|string|in:day,week,month',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'Status' => 'Bad Request',
                'Errors' => 'Некорректные параметры запроса',
            ], 400);
        }

        return null;
    }
}

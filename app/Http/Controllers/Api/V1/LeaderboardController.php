<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ScoreLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function getTopUsers(Request $request): JsonResponse
    {
        $period = $request->input('period', 'day');
        $this->validatePeriod($period);
        $startDate = $this->getStartDate($period);

        $topUsers = ScoreLog::select('user_id', DB::raw('SUM(points) as total_points'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get()
            ->map(function ($log, $index) {
                $user = User::find($log->user_id);

                return [
                    'position' => $index + 1,
                    'user_id' => $log->user_id,
                    'username' => $user->username,
                    'score' => (int) $log->total_points,
                ];
            });

        return response()->json([
            'period' => $startDate,
            'top' => $topUsers,
        ], 200);
    }

    public function getUserRank(Request $request, $userId): JsonResponse
    {
        $period = $request->input('period', 'day');
        $this->validatePeriod($period);
        $startDate = $this->getStartDate($period);

        $userScore = ScoreLog::where('user_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->sum('points');

        $rank = ScoreLog::select('user_id', DB::raw('SUM(points) as total_points'))
            ->where('created_at', '>=', $period)
            ->groupBy('user_id')
            ->having('total_points', '>', $userScore)
            ->count() + 1;

        return response()->json([
            'user_id' => (int) $userId,
            'period' => $period,
            'score' => (int) $userScore,
            'rank' => $rank,
        ]);
    }

    private function validatePeriod($period): void
    {
        if (! in_array($period, ['day', 'week', 'month'])) {
            response()->json([
                'status' => 'error',
                'message' => 'Некорректные параметры запроса',
            ], 400);
        }
    }

    private function getStartDate($period): Carbon
    {
        return match ($period) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            default => Carbon::now()->subDay(),
        };
    }
}

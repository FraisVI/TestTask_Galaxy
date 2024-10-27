<?php

namespace App\Http\Controllers;

use App\Models\ScoreLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function top(Request $request)
    {
        $period = $request->input('period', 'day');
        $startDate = $this->getStartDate($period);

        $topUsers = ScoreLog::select('user_id', DB::raw('SUM(points) as total_points'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get();

        return response()->json($topUsers);
    }

    public function rank(Request $request, $userId)
    {
        $period = $request->input('period', 'day');
        $startDate = $this->getStartDate($period);

        $userScore = ScoreLog::where('user_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->sum('points');

        $rank = ScoreLog::select('user_id', DB::raw('SUM(points) as total_points'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('user_id')
                ->having('total_points', '>', $userScore)
                ->count() + 1;

        return response()->json([
            'user_id' => $userId,
            'rank' => $rank
        ]);
    }

    private function getStartDate($period)
    {
        return match ($period) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            default => Carbon::now()->subDay(),
        };
    }
}

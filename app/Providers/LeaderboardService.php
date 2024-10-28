<?php

namespace App\Providers;

use App\Models\ScoreLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class LeaderboardService extends ServiceProvider
{
    public function getTopUsers($period = 'day')
    {
        $startDate = $this->getStartDate($period);

        return ScoreLog::select('user_id', DB::raw('SUM(points) as total_points'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get();
    }

    public function getUserRank($userId, $period = 'day')
    {
        $startDate = $this->getStartDate($period);
        $userScore = ScoreLog::where('user_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->sum('points');

        return ScoreLog::select('user_id', DB::raw('SUM(points) as total_points'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->having('total_points', '>', $userScore)
            ->count() + 1;
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
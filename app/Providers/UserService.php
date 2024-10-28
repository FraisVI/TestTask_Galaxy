<?php

namespace App\Providers;

use App\Models\ScoreLog;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class UserService extends ServiceProvider
{
    public function createUser(array $data)
    {
        return User::create(['username' => $data['username']]);
    }

    public function addScore($userId, $points)
    {
        $user = User::find($userId);
        if (! $user) {
            return null;
        }
        ScoreLog::create([
            'user_id' => $user->id,
            'points' => $points,
        ]);
        $user->increment('total_score', $points);

        return $user->total_score;
    }
}

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

    public function addScore($userId, array $data)
    {
        $user = User::find($userId);
        if (! $user) {
            return null;
        }

        return ScoreLog::create([
            'user_id' => $user->id,
            'points' => $data['points'],
        ]);
    }
}

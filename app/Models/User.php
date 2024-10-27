<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $fillable = ['username'];

    public function scores() {
        return $this->hasMany(ScoreLog::class);
    }
}

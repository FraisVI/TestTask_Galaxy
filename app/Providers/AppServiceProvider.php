<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрация UserService
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService($app);
        });

        // Регистрация LeaderboardService
        $this->app->singleton(LeaderboardService::class, function ($app) {
            return new LeaderboardService($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

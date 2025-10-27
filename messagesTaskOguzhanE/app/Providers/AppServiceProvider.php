<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\MessageRepositoryInterface;
use App\Repositories\EloquentMessageRepository;
use Illuminate\Cache\RateLimiting\Limit; //istenilen 5x2 methodu için gerekli 
use Illuminate\Support\Facades\RateLimiter; //istenilen 5x2 methodu için gerekli 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            MessageRepositoryInterface::class,
            EloquentMessageRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('sms-gateway', function ($job) {
            // 5saniyede 2 dakikada 24
            return Limit::perMinute(24);
        });
    }
}

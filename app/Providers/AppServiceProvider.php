<?php

namespace App\Providers;

use App\Services\Abstract\ProfileServiceInterface;
use App\Services\Implementation\ProfileService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProfileServiceInterface::class, ProfileService::class);  
      }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('apiRateLimit', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}

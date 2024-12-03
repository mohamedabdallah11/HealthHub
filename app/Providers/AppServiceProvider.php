<?php

namespace App\Providers;

use App\Services\Abstract\ProfileServiceInterface;
use App\Services\Implementation\ProfileService;
use Illuminate\Support\ServiceProvider;

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
        //
    }
}

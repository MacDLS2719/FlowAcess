<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind(LoginResponse::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
          
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
   }


}

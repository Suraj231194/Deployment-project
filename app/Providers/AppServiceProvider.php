<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse as FortifyLoginResponse;
use App\Http\Responses\RegisterResponse as FortifyRegisterResponse;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponse::class, FortifyLoginResponse::class);
        $this->app->singleton(RegisterResponse::class, FortifyRegisterResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

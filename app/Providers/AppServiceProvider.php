<?php

namespace App\Providers;

use App\Http\Resources\UserResource;
use App\Http\Responses\UserResponse;
use App\Services\RegisterService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Sanctum::ignoreMigrations();
        $this->app->bind(RegisterService::class, fn() => new RegisterService());
        $this->app->bind(UserResponse::class, fn() => new UserResponse());
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        UserResource::withoutWrapping();
        Schema::defaultStringLength(191);
    }
}

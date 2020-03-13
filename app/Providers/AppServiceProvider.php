<?php

namespace App\Providers;

use App\Factories\AuthyFactory;
use App\Services\AuthyApi;
use App\Services\AuthyAuthentication;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthyAuthentication::class, function () {
            return new AuthyAuthentication(new AuthyApi(env('AUTHY_SECRET_KEY')), new AuthyFactory());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

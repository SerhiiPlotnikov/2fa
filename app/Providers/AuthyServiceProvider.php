<?php

namespace App\Providers;

use App\Services\AuthyApi;
use App\Services\AuthyAuthentication;
use Illuminate\Support\ServiceProvider;

class AuthyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(AuthyAuthentication::class, function () {
            return new AuthyAuthentication(new AuthyApi(env('AUTHY_SECRET_KEY')));
        });
    }
}

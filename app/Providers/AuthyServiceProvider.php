<?php

namespace App\Providers;

use App\Services\AuthyService;
use Authy\AuthyApi;
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
        $this->app->singleton(AuthyService::class, function () {
            return new AuthyService(new AuthyApi(env('AUTHY_SECRET')));
        });
    }
}

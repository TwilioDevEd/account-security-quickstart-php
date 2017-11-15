<?php

namespace App\Providers;

use App\Library\Services\PhoneVerification;
use Authy\AuthyApi;
use Illuminate\Support\ServiceProvider;

class AuthyApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $api_key = config('app.authy_api_key');
        $this->app->bind('Authy\AuthyApi', function () use ($api_key) {
            return new AuthyApi(env('API_KEY'));
        });
    }
}

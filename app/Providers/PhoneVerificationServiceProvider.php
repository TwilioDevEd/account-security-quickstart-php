<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\PhoneVerification;

class PhoneVerificationServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Library\Services\PhoneVerification', function ($app) {
            return new PhoneVerification();
        });
    }
}

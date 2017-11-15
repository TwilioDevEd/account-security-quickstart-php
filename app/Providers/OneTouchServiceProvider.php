<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\OneTouch;

class OneTouchServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Library\Services\OneTouch', function ($app) {
            return new OneTouch();
        });
    }
}

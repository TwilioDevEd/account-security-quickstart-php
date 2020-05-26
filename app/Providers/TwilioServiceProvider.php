<?php

namespace App\Providers;

use App\Library\Services\PhoneVerification;
use Twilio\Rest\Client;
use Illuminate\Support\ServiceProvider;

class TwilioServiceProvider extends ServiceProvider
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
        $this->app->bind(Client::class, function () {
          $sid = config('app.twilio.account_sid');
          $token = config('app.twilio.auth_token');
          return new Client($sid, $token);
      });
    }
}

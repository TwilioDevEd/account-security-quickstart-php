<a href="https://www.twilio.com">
  <img src="https://static0.twilio.com/marketing/bundles/marketing/img/logos/wordmark-red.svg" alt="Twilio" width="250" />
</a>

# Twilio Account Security Quickstart - Two-Factor Authentication and Phone Verification
> We are currently in the process of updating this sample template. If you are encountering any issues with the sample, please open an issue at [github.com/twilio-labs/code-exchange/issues](https://github.com/twilio-labs/code-exchange/issues) and we'll try to help you.


A simple **PHP**, **Laravel** and **AngularJS** implementation of a website that uses Twilio Account
Security services to protect all assets within a folder. Additionally, it shows a Phone
Verification implementation.

It uses four channels for delivery, SMS, Voice, Soft Tokens, and Push Notifications.
You should have the [Authy App](https://authy.com/download/) installed to try Soft Token
and Push Notification support.

#### Two-Factor Authentication Demo

- URL path "/protected" is protected with both user session and Twilio Two-Factor Authentication
- One Time Passwords (SMS and Voice)
- SoftTokens
- Push Notifications (via polling)

#### Phone Verification

- Phone Verification
- SMS or Voice Call

### Run it
#### Pre-requisites

- [PHP](http://php.net/archive/2017.php#id2017-10-27-1)
- [Composer](https://getcomposer.org/)
- [Laravel](https://laravel.com/docs/5.5/#installing-laravel)
- [MySQL](https://www.mysql.com/)

Ensure your MySQL credentials and host are set up in the env file, which you copy from `.env.example`.


1. Clone this repo
    ```bash
    git clone git@github.com:TwilioDevEd/account-security-quickstart-php.git
    ```
1. `composer install`
1. Register for a [Twilio Account](https://www.twilio.com/).
1. Setup an Account Security app via the [Twilio Console](https://twilio.com/console).
1. `cp .env.example .env`
1. Grab an Application API key from the [Dashboard](https://www.twilio.com/console/authy/getting-started) and paste it in `.env` as `API_KEY`
1. `source .env` on *nix
1. Run `php artisan migrate`
1. Run `php artisan serve --port 8081`
1. Go to localhost:8081 or see below.

### To try Authy Two-Factor Authentication
1. Open the following url in your browser: `http://localhost:8081/login`

At that point you can test a channel. To test another, simply logout after your success and login again.

### To try Verify Phone Verification
1. Open the following url in your browser: `http://localhost:8081/verify`

At that point you can test SMS/Phone Calls. To test another, simply logout after your success. You'll be brought back to the index page to try again with the other.

### Test

1. `phpunit`

### License
- MIT

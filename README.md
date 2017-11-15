<a href="https://www.twilio.com">
  <img src="https://static0.twilio.com/marketing/bundles/marketing/img/logos/wordmark-red.svg" alt="Twilio" width="250" />
</a>

[![Docker Build Status](https://img.shields.io/docker/build/jrottenberg/ffmpeg.svg)]()

# Twilio Account Security Quickstart - Two-Factor Authentication and Phone Verification

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

### Run it (the easy way)

1. Install [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/install/)
1. `cp .env.example .env`
1. Grab an Application API key from the [Dashboard](https://www.twilio.com/console/authy/getting-started) and paste it in `.env` as `API_KEY`
1. `./start`

## Run without Docker
### Pre-requisites

- [PHP](http://php.net/archive/2017.php#id2017-10-27-1)
- [Composer](https://getcomposer.org/)
- [Laravel](https://laravel.com/docs/5.5/#installing-laravel)
- [MySQL](https://www.mysql.com/), ensure it is running on port `3306` for username, etc
see `.env.example` file

### Run

1. Clone this repo
    ```bash
    git clone git@github.com:TwilioDevEd/account-security-quickstart-php.git
    ```
1. `composer install`
1. Register for a [Twilio Account](https://www.twilio.com/).
1. Setup an Account Security app via the [Twilio Console](https://twilio.com/console).
1. `cp .env.example .env`
1. Grab an Application API key from the [Dashboard](https://www.twilio.com/console/authy/getting-started) and paste it in `.env` as `API_KEY`
1. Run `php artisan migrate`
1. Run `php artisan serve --port 8081`
1. Open the following url in your browser: `http://localhost:8081`

### Test

1. Install [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/install/)
1. `./test`

### License
- MIT

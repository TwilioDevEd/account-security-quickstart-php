<?php

use App\Http\Controllers\Auth\PhoneVerificationController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/api/user/register', 'Auth\RegisterController@create');

Route::prefix('api/verification/')->group(function () {
    Route::post('start', 'Auth\PhoneVerificationController@startVerification');
    Route::post('verify', 'Auth\PhoneVerificationController@verifyCode');
});

Route::prefix('api/')->middleware(['sessions', 'api'])->group(function () {
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('/logout', 'Auth\LoginController@logout');
});

Route::prefix('api/accountsecurity')->middleware(['sessions','auth','api'])->group(function () {
    Route::post('/onetouch', 'Auth\AccountSecurityController@createOneTouch');
    Route::post('/onetouchstatus', 'Auth\AccountSecurityController@checkOneTouchStatus');
    Route::post('/sms', 'Auth\AccountSecurityController@sendVerificationCodeSMS');
    Route::post('/verify', 'Auth\AccountSecurityController@verifyToken');
    Route::post('/voice', 'Auth\AccountSecurityController@sendVerificationCodeVoice');
});

Route::prefix('/protected')->middleware(['web'])->group(function () {
});

Route::prefix('/2fa')->middleware(['web', '2fa'])->group(function () {
});

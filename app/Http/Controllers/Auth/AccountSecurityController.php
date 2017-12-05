<?php

namespace App\Http\Controllers\Auth;

use \Exception;
use App\Http\Controllers\Controller;
use App\Library\Services\OneTouch;
use App\User;
use Authy\AuthyApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AccountSecurityController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Account Security Controller
    |--------------------------------------------------------------------------
    |
    | Uses Authy to verify a users phone via voice, sms or notifications.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * send verification code via SMS.
     *
     * @param  Illuminate\Support\Facades\Request  $request;
     * @return Illuminate\Support\Facades\Response;
     */
    protected function sendVerificationCodeSMS(
        Request $request,
        AuthyApi $authyApi
    ) {
        $authyID = Auth::user()['authyID'];
        $response = $authyApi->requestSms($authyID, ['force' => 'true']);

        if ($response->ok()) {
            return response()->json(['message' => 'Verification Code sent via SMS succesfully.']);
        } else {
            return response()->json($response->errors(), 500);
        }
    }


   /**
    * send verification code via Voice Call.
    *
    * @param  Illuminate\Support\Facades\Request  $request;
    * @return Illuminate\Support\Facades\Response;
    */
    protected function sendVerificationCodeVoice(
        Request $request,
        AuthyApi $authyApi
    ) {
        $authyID = Auth::user()['authyID'];
        $response = $authyApi->requestSms($authyID, ['force' => 'true']);
        $authyApi->phoneCall($authyID, ['force' => 'true']);

        if ($response->ok()) {
            return response()->json(['message' => 'Verification Code sent via Voice Call succesfully.']);
        } else {
            return response()->json($response->errors(), 500);
        }
    }

   /**
    * verify token.
    *
    * @param  Illuminate\Support\Facades\Request  $request;
    * @return Illuminate\Support\Facades\Response;
    */
    protected function verifyToken(
        Request $request,
        AuthyApi $authyApi
    ) {
        $data = $request->all();
        $token = $data['token'];
        $authyID = Auth::user()['authyID'];

        $response = $authyApi->verifyToken($authyID, $token);

        if ($response->ok()) {
            return response()->json(['message' => 'Token verified successfully.']);
        } else {
            return response()->json($response->errors(), 500);
        }
    }

   /**
    * Create One Touch Approval Request.
    *
    * @param  Illuminate\Support\Facades\Request  $request;
    * @return Illuminate\Support\Facades\Response;
    */
    protected function createOneTouch(
        Request $request,
        AuthyApi $authyApi
    ) {
        $user = Auth::user();
        $authyID = $user['authyID'];
        $username =$user['username'];


        $response = $authyApi->oneTouchVerificationRequest(
            $authyID,
            'Twilio Account Security Quickstart wants authentication approval.',
            120,
            [
                'username' => $username,
                'AuthyID' => $authyID,
                'Location' => 'San Francisco, CA',
                'Reason' => 'Demo by Account Security'
            ]
        );

        if ($response->ok()) {
            $approval_request = (array)$response->bodyvar('approval_request');
            session(['onetouch_uuid' => $approval_request['uuid']]);

            return response()->json(['message' => 'Token verified successfully.']);
        } else {
            return response()->json($response->errors(), 500);
        }
    }

   /**
    * Get OneTouch Status.
    *
    * @param  Illuminate\Support\Facades\Request  $request;
    * @return Illuminate\Support\Facades\Response;
    */
    protected function checkOneTouchStatus(
        Request $request,
        AuthyApi $authyApi
    ) {
        $authyID = Auth::user()['authyID'];
        $response = $authyApi->oneTouchVerificationCheck($authyID, session('onetouch_uuid'));

        if ($response->ok()) {
            $approval_request = (array)$response->bodyvar('approval_request');
            session(['authy' => $approval_request['status']]);

            return response()->json($response->body(), 200);
        } else {
            return response()->json($response->errors(), 500);
        }
    }
}

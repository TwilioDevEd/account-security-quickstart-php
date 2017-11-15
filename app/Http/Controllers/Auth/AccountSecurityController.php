<?php

namespace App\Http\Controllers\Auth;

use \Exception;
use App\Http\Controllers\Controller;
use App\Library\Services\OneTouch;
use App\User;
use Authy\AuthyApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $user_id = session('user_id');
        $user = User::find($user_id);
        $authyID = $user['authyID'];
        $authyApi->requestSms($authyID, ['force' => 'true']);

        return response()->json(['message' => 'Verification Code sent via SMS succesfully.']);
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
        $user_id = session('user_id');
        $user = User::find($user_id);
        $authyID = $user['authyID'];
        $authyApi->phoneCall($authyID, ['force' => 'true']);

        return response()->json(['message' => 'Verification Code sent via Voice Call succesfully.']);
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
        $user_id = session('user_id');
        $user = User::find($user_id);
        $authyID = $user['authyID'];
        $authyApi->verifyToken($authyID, $token);

        return response()->json(['message' => 'Token verified successfully.']);
    }

   /**
    * Create One Touch Approval Request.
    *
    * @param  Illuminate\Support\Facades\Request  $request;
    * @return Illuminate\Support\Facades\Response;
    */
    protected function createOneTouch(
        Request $request,
        OneTouch $oneTouch
    ) {
        $data = $request->all();
        $user_id = session('user_id');
        $user = User::find($user_id);
        $authyID = $user['authyID'];
        $username =$user['username'];

        $data = [
          'message' => 'Twilio Account Security Quickstart wants authentication approval.',
          'hidden' => 'this is a hidden value',
          'visible' => [
            'username' => $username,
            'AuthyID' => $authyID,
            'Location' => 'San Francisco, CA',
            'Reason' => 'Demo by Account Security'
          ],
          'seconds_to_expire' => 120
        ];

        $onetouch_uuid = $oneTouch->createApprovalRequest($data);
        session(['onetouch_uuid' => $onetouch_uuid]);

        return response()->json(['message' => 'Token verified successfully.']);
    }

   /**
    * Get OneTouch Status.
    *
    * @param  Illuminate\Support\Facades\Request  $request;
    * @return Illuminate\Support\Facades\Response;
    */
    protected function checkOneTouchStatus(
        Request $request,
        OneTouch $oneTouch
    ) {
        $response = $oneTouch->oneTouchStatus(session('onetouch_uuid'));

        session(['authy' => $body['approval_request']['status']]);

        return response()->json($response, 200);
    }
}

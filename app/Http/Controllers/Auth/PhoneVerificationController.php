<?php

namespace App\Http\Controllers\Auth;

use \Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;


class PhoneVerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Phone Verification  Controller
    |--------------------------------------------------------------------------
    |
    | Uses Authy to verify a users phone via voice or sms.
    |
    */

    /**
     * @var Client
     */
    private $client;


    /**
     * @var string
     */
    private $verification_sid;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Client $client, string $verification_sid = null)
    {
        $this->middleware('guest');
        $this->client = $client;
        $this->verification_sid = $verification_sid ?: config('app.twilio.verification_sid');

    }

    /**
     * Get a validator for an incoming verification request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function verificationRequestValidator(array $data)
    {
        return Validator::make($data, [
            'phone_number' => 'required|string',
            'via' => 'required|string|max:4',
        ]);
    }

    /**
     * Get a validator for an code verification request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function verificationCodeValidator(array $data)
    {
        return Validator::make($data, [
            'phone_number' => 'required|string',
            'token' => 'required|string|max:10'
        ]);
    }

    /**
     * Request phone verification via PhoneVerificationService.
     *
     * @param  array  $data
     * @return Illuminate\Support\Facades\Response;
     */
    protected function startVerification(
        Request $request
    ) {
        $data = $request->all();
        $validator = $this->verificationRequestValidator($data);
        extract($data);

        if ($validator->passes()) {
            try {
                $verification = $this->client->verify->v2->services($this->verification_sid)
                ->verifications
                ->create($phone_number, $via);
                return response()->json($verification->sid, 200);
            } catch (TwilioException $exception) {
                $message = "Verification failed to start: {$exception->getMessage()}";
                return response()->json($message, 400);
            }
        }

        return response()->json(['errors'=>$validator->errors()], 403);
    }

    /**
     * Request phone verification via PhoneVerificationService.
     *
     * @param  array  $data
     * @return Illuminate\Support\Facades\Response;
     */
    protected function verifyCode(
        Request $request
    ) {
        $data = $request->all();
        $validator = $this->verificationCodeValidator($data);
        extract($data);

        if ($validator->passes()) {
            try {
                $verification_check = $this->client->verify->v2->services($this->verification_sid)
                            ->verificationChecks
                            ->create($token, ['to' => $phone_number]);
                if ($verification_check->status === 'approved') {
                    return response()->json($verification_check->sid, 200);
                }
                throw new Exception('OTP verification failed');
            } catch (TwilioException $e) {
                $response=[];
                $response['exception'] = get_class($e);
                $response['message'] = $e->getMessage();
                $response['trace'] = $e->getTrace();
                return response()->json($response, 403);
            }

        }

        return response()->json(['errors'=>$validator->errors()], 403);
    }
}

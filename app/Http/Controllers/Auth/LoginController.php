<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {   
        $input = $request->all();

        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        if(Auth::guard()->attempt(array('username' => $input['username'], 'password' => $input['password'])))
        {
            return $this->sendLoginResponse($request);
        }
        
        return redirect('/')->with('error','Email-Address And Password Are Wrong.');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        isset($request->refresh_token) ?
            $refreshToken = $request->refresh_token : $refreshToken = null;
        $request->session()->regenerate();

        return $this->authenticated($request, Auth::guard()->user());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return response()->json(['message' => 'Logged in.'], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();

        $request->session()->invalidate();

        return response()->json(null, 200);

    }
}

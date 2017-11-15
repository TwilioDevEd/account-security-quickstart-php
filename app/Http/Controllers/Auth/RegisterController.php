<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Authy\AuthyApi;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'country_code' => 'required|string|max:4',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|max:11',
            'username' => 'required|string|max:255|unique:users',
        ]);

        $validator->after(function ($validator) {
            if ($this->usernameExists($validator->attributes()['username'])) {
                $validator->errors()->add('username', 'Username already registered.');
            }
        });

        return $validator;
    }

    /**
     * Check if user exists.
     *
     * @param string  $username
     */
    protected function usernameExists(string $username)
    {
        return User::where('username', $username)->first();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(Request $request, AuthyApi $authyApi)
    {
        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $validator = $this->validator($data);

        if ($validator->passes()) {
            extract($data);
            $user = $authyApi->registerUser($email, $phone_number, $country_code);

            if ($user->ok()) {
                $data['authyID'] = $user->id();
            }

            $user = User::create($data);

            // Login the user
            Auth::login($user, true);

            $user_id = session('user_id');

            return $user;
        }

        return response()->json(['errors'=>$validator->errors()], 403);
    }
}

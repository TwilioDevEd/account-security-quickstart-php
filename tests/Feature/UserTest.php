<?php

namespace Tests\Feature;

use \Authy\AuthyApi;
use \Mockery;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        // $this->withoutExceptionHandling();
        $this->mockedAuthyApi = Mockery::mock(AuthyApi::class);
        app()->instance(AuthyApi::class, $this->mockedAuthyApi);
    }

    public function testRegisterUserSucceeds()
    {
        $phoneNumber = '7075555555';
        $countryCode = '1';

        $params = [
            'country_code' => $countryCode,
            'email' => 'email@example.com',
            'password' => 'XXXXXXXX',
            'phone_number' => $phoneNumber,
            'username' => 'user_xxx',
        ];

        $mockAuthyUser = Mockery::mock(AuthyUser::class);
        $mockAuthyUser->shouldReceive(['ok' => true, 'id' => 'xxxx'])
            ->once();

        $this->mockedAuthyApi
            ->shouldReceive(['registerUser' => $mockAuthyUser])
            ->once();

        $response = $this->post('api/user/register', $params);
        $response->assertStatus(200);
    }

    public function testLoginSucceeds()
    {

        $phoneNumber = '7075555555';
        $countryCode = '1';
        $password = 'XXXXXXXX';
        $username = 'user_xxx';

        $user_data = [
            'country_code' => $countryCode,
            'email' => 'email2@example.com',
            'password' => bcrypt($password),
            'phone_number' => $phoneNumber,
            'username' => $username,
        ];

        $user = User::create($user_data);
        $user->save();

        $login_params = [
          'username' => $username,
          'password' => $password
        ];

        $response = $this->post('/api/login', $login_params);

        $response->assertStatus(200);
    }

    public function testLoginFails()
    {

        $phoneNumber = '7075555555';
        $countryCode = '1';
        $password = 'XXXXXXXX';
        $username = 'user_xxx';

        $user_data = [
            'country_code' => $countryCode,
            'email' => 'email2@example.com',
            'password' => bcrypt($password),
            'phone_number' => $phoneNumber,
            'username' => $username,
        ];

        $user = User::create($user_data);
        $user->save();

        $login_params = [
          'username' => $username,
          'password' => 'yyyyyyyy'
        ];

        $response = $this->post('/api/login', $login_params);

        $response->assertStatus(302);
    }

    public function testLogoutSucceeds()
    {
        $response = $this->get('/api/logout');
        $response->assertStatus(200);
    }
}

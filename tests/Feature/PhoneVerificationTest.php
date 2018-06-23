<?php

namespace Tests\Feature;

use \Mockery;
use Authy\AuthyApi;
use Authy\AuthyResponse;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneVerificationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->mockedAuthyApi = Mockery::mock(AuthyApi::class);
        app()->instance(AuthyApi::class, $this->mockedAuthyApi);
    }

    public function testStartVerificationSucceeds()
    {
        $phoneNumber = '7075555555';
        $countryCode = '1';

        $params = [
          'country_code' => $countryCode,
          'phone_number' => $phoneNumber,
          'via' => 'sms'
        ];

        $this->mockedAuthyApi
            ->shouldReceive([
                'phoneVerificationStart' => [
                    'carrier' => 'Radiomovil Dipsa (Telcel/America Movil)',
                    'is_cellphone' => true,
                    'message' => "SMS Message sent to +{$countryCode} {$phoneNumber}.",
                    'seconds_to_expire' => 599,
                    'success' => true,
                    'uuid' => 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX',
                ]
            ])
            ->andReturn(new AuthyResponse(new Response(200, [], Psr7\stream_for('{"message": "success."}'))))
            ->once()
            ->with($phoneNumber, $countryCode, 'sms');

        $response = $this->post('/api/verify/start', $params);
        $response->assertStatus(200);
    }

    public function testStartVerficationFails()
    {
        $phoneNumber = '70755555555';
        $countryCode = '1';

        $params = [
            'country_code' => $countryCode,
            'phone_number' => $phoneNumber,
            'via' => 'sms'
        ];

        $this->mockedAuthyApi
            ->shouldReceive('phoneVerificationStart')
            ->never();

        $response = $this->post('/api/verify/start', $params);
        $response->assertStatus(403);
    }

    public function testCodeVerificationSucceeds()
    {
        $token = 'XXXX';
        $phoneNumber = '7075555555';
        $countryCode = '1';

        $params = [
            'country_code' => $countryCode,
            'phone_number' => $phoneNumber,
            'token' => $token
        ];

        $this->mockedAuthyApi
            ->shouldReceive(['phoneVerificationCheck' => []])
            ->once()
            ->with($phoneNumber, $countryCode, $token);


        $response = $this->post('/api/verify/verify', $params);
        $response->assertStatus(200);
    }

    public function testCodeVerificationFails()
    {
        $token = 'XXXX';
        $phoneNumber = '70755555555';
        $countryCode = '1';

        $params = [
            'country_code' => $countryCode,
            'phone_number' => $phoneNumber,
            'token' => $token
        ];

        $this->mockedAuthyApi
            ->shouldReceive(['phoneVerificationCheck' => ''])
            ->never();

        $response = $this->post('/api/verify/verify', $params);
        $response->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use \Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Twilio\Rest\Client;

class PhoneVerificationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function testStartVerificationSucceeds()
    {
        $params = [
            'phone_number' => '+17075555555',
            'via' => 'sms'
        ];

        $this->mock(Client::class, function ($mock) {
            $mockVerifications = Mockery::mock();
            $mockVerifications
                ->shouldReceive('create')
                ->with('+17075555555', 'sms')
                ->andReturn((object) ['sid' => 1])
                ->once();

            $mockServicesReturn = Mockery::mock();
            $mockServicesReturn->verifications = $mockVerifications;

            $mockV2 = Mockery::mock();
            $mockV2
                ->shouldReceive('services')
                ->withAnyArgs()
                ->andReturn($mockServicesReturn)
                ->once();

            $mockVerify = Mockery::mock();
            $mockVerify->v2 = $mockV2;
            $mock->verify = $mockVerify;
        });

        $response = $this->post('/api/verify/start', $params);
        $response->assertStatus(200);
    }

    public function testStartVerficationFails()
    {
        $params = [
            'phone_number' => '+17075555555',
            'via' => 'not-valid-via'
        ];

        $this->mock(Client::class, function ($mock) {
            $mockVerifications = Mockery::mock();
            $mockVerifications
                ->shouldReceive('create')
                ->never();

            $mockServicesReturn = Mockery::mock();
            $mockServicesReturn->verifications = $mockVerifications;

            $mockV2 = Mockery::mock();
            $mockV2
                ->shouldReceive('services')
                ->never();

            $mockVerify = Mockery::mock();
            $mockVerify->v2 = $mockV2;
            $mock->verify = $mockVerify;
        });

        $response = $this->post('/api/verify/start', $params);
        $response->assertStatus(403);
    }

    public function testCodeVerificationSucceeds()
    {
        $params = [
            'phone_number' => '+17075555555',
            'token' => 'XXXX',
        ];

        $this->mock(Client::class, function ($mock) {
            $mockVerifications = Mockery::mock();
            $mockVerifications
                ->shouldReceive('create')
                ->with('XXXX', ['to' => '+17075555555'])
                ->andReturn((object) ['sid' => 1, 'status' => 'approved'])
                ->once();

            $mockServicesReturn = Mockery::mock();
            $mockServicesReturn->verificationChecks = $mockVerifications;

            $mockV2 = Mockery::mock();
            $mockV2
                ->shouldReceive('services')
                ->withAnyArgs()
                ->andReturn($mockServicesReturn)
                ->once();

            $mockVerify = Mockery::mock();
            $mockVerify->v2 = $mockV2;
            $mock->verify = $mockVerify;
        });


        $response = $this->post('/api/verify/verify', $params);
        $response->assertStatus(200);
    }

    public function testCodeVerificationFails()
    {
        $params = [
            'phone_number' => '+17075555555',
            'token' => 'super-long-invalid-token',
        ];

        $this->mock(Client::class, function ($mock) {
            $mockVerifications = Mockery::mock();
            $mockVerifications
                ->shouldReceive('create')
                ->never();

            $mockServicesReturn = Mockery::mock();
            $mockServicesReturn->verificationChecks = $mockVerifications;

            $mockV2 = Mockery::mock();
            $mockV2
                ->shouldReceive('services')
                ->never();

            $mockVerify = Mockery::mock();
            $mockVerify->v2 = $mockV2;
            $mock->verify = $mockVerify;
        });


        $response = $this->post('/api/verify/verify', $params);
        $response->assertStatus(403);
    }
}

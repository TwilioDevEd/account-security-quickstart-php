<?php
namespace App\Library\Services;

use GuzzleHttp\Client;

class PhoneVerification
{
    private $authyBaseUrl = 'https://api.authy.com';
    private $requestVerificationUrl = '/protected/json/phones/verification/start';
    private $verifyCodeUrl = '/protected/json/phones/verification/check';

    protected function __construct()
    {
        $phpVersion = phpversion();
        $this->userAgent = "PhoneVericationPHPReg (PHP {$phpVersion})";
        $this->authyApiKey = config('app.authy_api_key');
        $this->headers = ['User-Agent' => $this->userAgent];
        $this->client = new Client();
    }

    private function getUrl(string $path)
    {
        return "{$this->authyBaseUrl}{$path}";
    }

    public function requestVerification(array $verificationData)
    {
        $verificationData['api_key'] = $this->authyApiKey;


        $response = $this->client->post($this->getUrl($this->requestVerificationUrl), [
            'headers' => $this->headers,
            'form_params' => $verificationData,
            'query' => ['api_key' => $this->authyApiKey]
        ]);

        $statusCode = $response->getStatusCode();

        return (string)$response->getBody();
    }

    public function verifyToken(array $data)
    {
        $data['api_key'] = $this->authyApiKey;
        $data['verification_code'] = $data['token'];
        unset($data['token']);

        $response = $this->client->post($this->getUrl($this->verifyCodeUrl), [
          'headers' => $this->headers,
          'form_params' => $data,
          'query' => ['api_key' => $this->authyApiKey]
        ]);

        return $response->getBody();
    }
}

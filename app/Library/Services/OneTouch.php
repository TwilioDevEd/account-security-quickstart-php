<?php
namespace App\Library\Services;

use GuzzleHttp\Client;

class OneTouch
{
    private $authyBaseUrl = 'https://api.authy.com';

    function __construct()
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

    public function createApprovalRequest(array $requestData)
    {
        $requestData['api_key'] = $this->authyApiKey;
        $path = "/onetouch/json/users/{$requestData['visible']['AuthyID']}/approval_requests";

        $response = $this->client->post($this->getUrl($path), [
            'headers' => $this->headers,
            'body' => $requestData
        ]);

        $statusCode = $response->getStatusCode();

        return $response->getBody()['approval_request']['uuid'];
    }

    public function oneTouchStatus(string $onetouch_uuid)
    {
        $data = [
            'api_key' => $this->authyApiKey
        ];
        $path = "/onetouch/json/approval_requests/{$onetouch_uuid}";

        $response = $this->client->post($this->getUrl($path), [
          'headers' => $this->headers,
          'body' => $data,
          'query' => ['api_key' => $this->authyApiKey]
        ]);

        return $response->getBody();
    }
}

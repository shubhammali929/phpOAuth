<?php
namespace Minioarage2\Phpoauth;
use \Exception;
class OAuthApiClient {
    private Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    // Automatically calls getUserInfo after getting the access token
    public function getAccessTokenWithPassword(string $username, string $password): array {
        $postData = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret()
        ];

        $tokenResponse = $this->makeApiCall('moas/rest/oauth/token', $postData, 'POST');
        
        if (!isset($tokenResponse['access_token'])) {
            throw new Exception('Access token not found in response.');
        }

        // Automatically call getUserInfo using the access token
        return $this->getUserInfo($tokenResponse['access_token']);
    }

    // Gets user info by making a GET request
    private function getUserInfo(string $accessToken): array {
        return $this->makeApiCall('moas/rest/oauth/getuserinfo', [], 'GET', $accessToken);
    }

    // Generalized API call method
    private function makeApiCall(string $endpoint, array $data, string $method, string $accessToken = null): ?array {
        $url = $this->config->getBaseUrl() . $endpoint;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $headers = ['Accept: application/json'];
        if ($accessToken) {
            $headers[] = 'Authorization: Bearer ' . $accessToken;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        return $decodedResponse;
    }
}

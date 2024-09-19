<?php
namespace Minioarage2\Phpoauth;
use \Exception;
require_once "makePostApiCall.php"; 
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

        // Use the makeCall function from makePostApiCall.php..
        $tokenResponse = makeCall($this->config->getBaseUrl() . 'moas/rest/oauth/token', [
            'Accept: application/json'
        ], $postData);
        
        if (!isset($tokenResponse['access_token'])) {
            throw new Exception('Access token not found in response.');
        }

        // Automatically call getUserInfo using the access token
        return $this->getUserInfo($tokenResponse['access_token']);
    }

    // Gets user info by making a GET request
    private function getUserInfo(string $accessToken): array {
        // Use makeCall function for the GET request
        return makeCall($this->config->getBaseUrl() . 'moas/rest/oauth/getuserinfo', [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken
        ], []);
    }
}

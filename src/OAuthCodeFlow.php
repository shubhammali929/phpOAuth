<?php
namespace Minioarage2\Phpoauth;
class OAuthCodeFlow {
    private Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function getAuthorizationUrl(): string {
        $clientId = $this->config->getClientId();
        $redirectUri = urlencode($this->config->getRedirectUri());
        $responseType = 'code'; // Implicit grant returns token directly
        $state = $_SESSION['state'];
        $scope = 'openid';
        return "{$this->config->getBaseUrl()}/moas/idp/openidsso?response_type={$responseType}&client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&state={$state}";
    }

    public function redirectToAuthorization(): void {
        $authUrl = $this->getAuthorizationUrl();
        header("Location: {$authUrl}");
        exit();
    }

 
}
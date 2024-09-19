<?php
namespace Minioarage2\Phpoauth;
class OAuthImplicitFlow {
    private Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function getAuthorizationUrl(): string {
        $clientId = $this->config->getClientId();
        $redirectUri = urlencode($this->config->getRedirectUri());
        $responseType = 'token'; // Implicit grant returns token directly
        $state = $_SESSION['state'];

        return "{$this->config->getBaseUrl()}/moas/idp/openidsso?response_type={$responseType}&client_id={$clientId}&redirect_uri={$redirectUri}&scope=email&state={$state}";
    }

    public function redirectToAuthorization(): void {
        $authUrl = $this->getAuthorizationUrl();
        header("Location: {$authUrl}");
        exit();
    }
}
<?php
namespace Minioarage2\Phpoauth;
use function Minioarage2\Phpoauth\generateCodeChallenge;
use function Minioarage2\Phpoauth\generateCodeVerifier;
class OAuthPkceFlow {
    private Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function getAuthorizationUrl(): string {
        $codeVerifier = generateCodeVerifier();
        $_SESSION['code_verifier'] = $codeVerifier;
        $codeChallenge = generateCodeChallenge($codeVerifier);

        $clientId = $this->config->getClientId();
        $redirectUri = urlencode($this->config->getRedirectUri());
        $responseType = 'code'; // Implicit grant returns token directly
        $state = $_SESSION['state'];
        $scope = 'openid';
        return "{$this->config->getBaseUrl()}/moas/idp/openidsso?response_type={$responseType}&client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&code_challenge={$codeChallenge}&code_challenge_method=S256&state={$state}";
    }

    public function redirectToAuthorization(): void {
        $authUrl = $this->getAuthorizationUrl();
        header("Location: {$authUrl}");
        exit();
    }
}
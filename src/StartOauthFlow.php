<?php
namespace Minioarage2\Phpoauth;
use function Minioarage2\Phpoauth\generateCodeChallenge;
use function Minioarage2\Phpoauth\generateCodeVerifier;
class StartOAuthFlow {
    private Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function getAuthorizationUrl(string $grantType): string {
        $clientId = $this->config->getClientId();
        $redirectUri = urlencode($this->config->getRedirectUri());
        $state = $_SESSION['state'] ?? bin2hex(random_bytes(16)); // Generate state if not already set
        $scope = 'openid';
        $responseType = 'code'; // Default response_type for Authorization Code and PKCE

        // Handle different grant types
        if ($grantType === 'implicit') {
            $responseType = 'token'; 
            $scope = 'email'; // we will get access token and then make req at getuserinfo endpoint....
        }

        // PKCE code challenge
        $codeChallengeParam = '';
        if ($grantType === 'pkce') {
            $codeVerifier = generateCodeVerifier();
            $_SESSION['code_verifier'] = $codeVerifier;
            $codeChallenge = generateCodeChallenge($codeVerifier);
            $codeChallengeParam = "&code_challenge={$codeChallenge}&code_challenge_method=S256";
        }

        // Construct the authorization URL
        return "{$this->config->getBaseUrl()}/moas/idp/openidsso?response_type={$responseType}&client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&state={$state}{$codeChallengeParam}";
    }

    //redirect user for authorisation..
    public function redirectToAuthorization(string $grantType): void {
        $authUrl = $this->getAuthorizationUrl($grantType);
        header("Location: {$authUrl}");
        exit();
    }
}

<?php
namespace Minioarage2\Phpoauth;
use function Minioarage2\Phpoauth\makeCall;
use function Minioarage2\Phpoauth\decodeToken;

function handleCodeAndState($auth_code, $state, $config, $loginSuccessListener) {
    $Storedstate = $_SESSION['state'];

    if ($state == $Storedstate) {
        $baseUri = $config->getBaseUrl(); // Assuming getBaseUrl() returns the base URI
        $url = $baseUri . '/moas/rest/oauth/token'; // Concatenate base URI with endpoint
        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];

        // Parameters for the token request
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $config->getClientId(),
            'client_secret' => $config->getClientSecret(),
            'redirect_uri' => $config->getRedirectUri(),
            'code' => $auth_code,
            'scope' => 'openid'
        ];

        // Get the code_verifier and grant_type from the session
        $code_verifier = $_SESSION['code_verifier'] ?? null;
        $grant_type = $_SESSION['grant_type'] ?? null;

        if ($grant_type === 'pkce' && $code_verifier !== null) {
            $params['code_verifier'] = $code_verifier;
        }

        try {
            // Make the API call
            $response = makeCall($url, $headers, $params);

            // Check if the ID token is present in the response
            if (!isset($response['id_token'])) {
                $loginSuccessListener->onError("ID token not found in the response for OAuth Flow...");
            }

            $id_token = $response['id_token'];
            $pem = $config->getPemCertificate();
            $decoded = decodeToken($pem, $id_token);

            if ($decoded !== null) {
                // Trigger the login success listener
                $loginSuccessListener->onLoginSuccess($decoded);
            } else {
                $loginSuccessListener->onError("Invalid Token ..");
            }
        } catch (\Exception $e) {
            // Handle the error and trigger onError
            $loginSuccessListener->onError($e->getMessage());
        }
    } else {
        // Handle invalid state and trigger onError
        $loginSuccessListener->onError('Invalid state.');
    }
}

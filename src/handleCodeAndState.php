<?php
namespace Minioarage2\Phpoauth;
use function Minioarage2\Phpoauth\makeCall;
use function Minioarage2\Phpoauth\decodeToken;

function handleCodeAndState($auth_code, $state, $config, $loginSuccessListener) {
    $Storedstate = $_SESSION['state'];

    if ($state == $Storedstate) {
        // Define the API call parameters
        $url = 'https://testshubham.miniorange.in/moas/rest/oauth/token';
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
                throw new \Exception('ID token not found in the response.');
            }

            $id_token = $response['id_token'];
            $pem = $config->getPemCertificate();
            $decoded = decodeToken($pem, $id_token);

            if ($decoded !== null) {
                // Trigger the login success listener
                $loginSuccessListener->onLoginSuccess($decoded);
            } else {
                throw new \Exception('Invalid token.');
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

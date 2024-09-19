<?php
namespace Minioarage2\Phpoauth;
require_once 'makePostApiCall.php';
require_once "utils.php";

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

        // Make the Aapi call
        $response = makeCall($url, $headers, $params);
        $id_token = $response['id_token'];
        $pem = $config->getPemCertificate();
        $decoded = decodeToken($pem, $id_token);

        if ($decoded !== null) {
            // Trigger the login success listener
            $loginSuccessListener->onLoginSuccess($decoded);
        } else {
            echo "Invalid token.";
        }
    } else {
        echo "Invalid state.";
    }
}
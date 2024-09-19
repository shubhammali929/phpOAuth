<?php
namespace Minioarage2\Phpoauth;

require_once "StartOAuthFlow.php";

function startAuthorisation($config, $grantType) {
    $_SESSION['grant_type'] = $grantType;
    $_SESSION['state'] = $_SESSION['state'] ?? bin2hex(random_bytes(16)); //generate random state and store it in session storeage for further verification..

    // Initialize OAuthFlow class and handle authorization
    $oauthFlow = new OAuthFlow($config);
    $oauthFlow->redirectToAuthorization($grantType);
}

<?php
namespace Minioarage2\Phpoauth;

// require_once "OAuthFlow.php";
use Minioarage2\Phpoauth\StartOAuthFlow;

function startAuthorisation($config, $grantType) {
    $_SESSION['grant_type'] = $grantType;
    $_SESSION['state'] = $_SESSION['state'] ?? bin2hex(random_bytes(16)); // Ensure state is generated for security

    // Initialize OAuthFlow class and handle authorization
    $oauthFlow = new StartOAuthFlow($config);
    $oauthFlow->redirectToAuthorization($grantType);
}

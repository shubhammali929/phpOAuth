<?php
namespace Minioarage2\Phpoauth;
use \Exception;
require_once 'config.php';
require_once 'OAuthApiClient.php';
require_once 'LoginSuccessListener.php'; 

function authenticateUserWithPasswordGrant($username, $password, $config, $loginSuccessListener) {
    $client = new OAuthApiClient($config); // Create OAuthApiClient instance

    try {
        // Automatically gets access token and user info
        $userInfo = $client->getAccessTokenWithPassword($username, $password);

        // Convert array to object
        $userInfoObject = json_decode(json_encode($userInfo));

        // Pass the object to the login success listener
        $loginSuccessListener->onLoginSuccess($userInfoObject);

    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

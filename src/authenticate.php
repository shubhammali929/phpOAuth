<?php
namespace Minioarage2\Phpoauth;

use \Exception;
use Minioarage2\Phpoauth\OAuthApiClient; 

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
        // Extract and decode the error message, then pass the "message" field to onError
        $loginSuccessListener->onError(
            json_decode($e->getMessage(), true)['message'] ?? 'An unknown error occurred.'
        );

    }
}
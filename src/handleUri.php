<?php
namespace Minioarage2\Phpoauth;
require_once "startAuthorisation.php";
require_once "handleCodeAndState.php";
require_once "utils.php";


function handleUri($config, $uri, $loginSuccessListener) {
    // Parse the uri 
    $parsedUrl = parse_url($uri);
    
    // Directly parse the query parameters
    $queryParams = [];
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $queryParams);
    }

    // Check for grant_type and start the authorization process accordingly..
    if (isset($queryParams['grant_type'])) {
        $grantType = $queryParams['grant_type'];
        startAuthorisation($config, $grantType);
    }
    
    // Handle id_token if present (for implicit)
    if (isset($queryParams["id_token"])) {
        $id_token = $queryParams["id_token"];
        $pem = $config->getPemCertificate();

        $decoded = decodeToken($pem, $id_token);
        
        if ($decoded !== null) {
            // Create an object to store decoded properties
            $decodedTokenObject = new \stdClass();
            
            // Loop through all properties in the decoded token
            foreach ($decoded as $key => $value) {
                // Store the properties in the object
                $decodedTokenObject->$key = $value;
                
                // Output the properties
                echo htmlspecialchars($key) . ': ' . htmlspecialchars(json_encode($value)) . '<br>';
            }

            // Call the onLoginSuccess method with the decoded token object
            $loginSuccessListener->onLoginSuccess($decodedTokenObject);

        } else {
            echo "Invalid token.";
        }
    }

    // Handle other query parameters like code and state if necessary
    if (isset($queryParams['code']) && isset($queryParams['state'])) {
        $code = $queryParams['code'];
        $state = $queryParams['state'];
        
        // Output the code and state
        echo "Authorization code: " . htmlspecialchars($code) . '<br>';
        echo "State: " . htmlspecialchars($state) . '<br>';
        
        handleCodeAndState($code, $state, $config, $loginSuccessListener);
    }
}

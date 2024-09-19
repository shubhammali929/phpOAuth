<?php
namespace Minioarage2\Phpoauth;
function makeCall(string $url, array $headers, array $params): array {
    $ch = curl_init($url);

    // Set CURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); // Converts parameters to URL-encoded format
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute the API call
    $response = curl_exec($ch);

    // Check for CURL errors
    if (curl_errno($ch)) {
        echo 'CURL Error: ' . curl_error($ch);
        return [];
    }

    // Close CURL session
    curl_close($ch);

    // Return the response decoded as an associative array
    return json_decode($response, true);
}
<?php
namespace Minioarage2\Phpoauth;
use \Exception;
require_once __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


function generateCodeVerifier(): string
{
    return rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '=');
}

function generateCodeChallenge(string $codeVerifier): string
{
    // Hash the verifier using SHA-256 and base64 URL-encode it
    return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
}

function verifyTokenSignature($jwt, $publicKey) {
    try {
        // Decode the JWT using the public key and verify its signature
        $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));
        return $decoded;
    } catch (Exception $e) {
        // Handle the exception if signature verification fails
        echo "Signature verification failed: " . $e->getMessage();
        return null;
    }
}

function decodeToken($pemCertificate, $id_token) {
    try {
        // Extract the public key from the PEM certificate
        $publicKey = openssl_pkey_get_public($pemCertificate);
        
        if ($publicKey === false) {
            throw new Exception('Failed to extract public key from PEM certificate.');
        }
        
        // Get the public key details
        $keyDetails = openssl_pkey_get_details($publicKey);
        
        if ($keyDetails === false || !isset($keyDetails['key'])) {
            throw new Exception('Failed to extract key details from the public key.');
        }
        
        $publicKeyString = $keyDetails['key'];
        
        // Decode the JWT using the extracted public key and verify its signature
        $decoded = JWT::decode($id_token, new Key($publicKeyString, 'RS256'));
        
        return $decoded;
    } catch (Exception $e) {
        // Handle the exception if decoding fails
        echo "Token decoding failed: " . $e->getMessage();
        return null;
    }
}


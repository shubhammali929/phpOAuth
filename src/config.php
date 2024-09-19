<?php
namespace Minioarage2\Phpoauth;
class Config {
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;
    private string $redirectUri;
    private string $pemCertificate;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $baseUrl,
        string $redirectUri,
        string $pemCertificate
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->baseUrl = $baseUrl;
        $this->redirectUri = $redirectUri;
        $this->pemCertificate = $pemCertificate;
    }

    public function getClientId(): string {
        return $this->clientId;
    }

    public function setClientId(string $clientId): void {
        $this->clientId = $clientId;
    }

    public function getClientSecret(): string {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): void {
        $this->clientSecret = $clientSecret;
    }

    public function getBaseUrl(): string {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): void {
        $this->baseUrl = $baseUrl;
    }

    public function getRedirectUri(): string {
        return $this->redirectUri;
    }

    public function setRedirectUri(string $redirectUri): void {
        $this->redirectUri = $redirectUri;
    }

    public function getPemCertificate(): string {
        return $this->pemCertificate;
    }

    public function setPemCertificate(string $pemCertificate): void {
        $this->pemCertificate = $pemCertificate;
    }
}

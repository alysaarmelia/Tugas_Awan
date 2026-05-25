<?php

namespace App\Libraries;

use Config\App;

/**
 * JWT Library — HS256 signing using native PHP.
 *
 * Handles:
 * - Token generation (access + refresh)
 * - Token validation & decoding
 * - Payload extraction
 */
class JwtLibrary
{
    private string $secret;
    private int $accessTtl;
    private int $refreshTtl;

    public function __construct()
    {
        $appConfig        = config('App');
        $this->secret     = $appConfig->jwtSecret ?? env('jwt.secret', 'default-secret');
        $this->accessTtl  = (int) (env('jwt.access_token_ttl') ?? 3600);
        $this->refreshTtl = (int) (env('jwt.refresh_token_ttl') ?? 2592000);
    }

    /**
     * Generate an access token.
     */
    public function generateAccessToken(int $userId, string $email): string
    {
        return $this->createToken($userId, $email, $this->accessTtl, 'access');
    }

    /**
     * Generate a refresh token.
     */
    public function generateRefreshToken(int $userId, string $email): string
    {
        return $this->createToken($userId, $email, $this->refreshTtl, 'refresh');
    }

    /**
     * Generate both tokens at once.
     */
    public function generateTokenPair(int $userId, string $email): array
    {
        return [
            'access_token'  => $this->generateAccessToken($userId, $email),
            'refresh_token' => $this->generateRefreshToken($userId, $email),
            'token_type'   => 'bearer',
            'expires_in'   => $this->accessTtl,
        ];
    }

    /**
     * Validate and decode a token.
     * Returns the payload on success, null on failure.
     */
    public function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$encodedHeader, $encodedPayload, $providedSig] = $parts;

        // Verify signature
        $expectedSig = $this->base64UrlEncode(
            hash_hmac('sha256', "$encodedHeader.$encodedPayload", $this->secret, true)
        );

        if (!hash_equals($expectedSig, $providedSig)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($encodedPayload), true);

        if (!$payload) {
            return null;
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Extract user ID from a valid token.
     */
    public function getUserIdFromToken(string $token): ?int
    {
        $payload = $this->validateToken($token);
        return $payload ? (int) ($payload['sub'] ?? null) : null;
    }

    /**
     * Check if token is a refresh token.
     */
    public function isRefreshToken(string $token): bool
    {
        $payload = $this->validateToken($token);
        return $payload && ($payload['type'] ?? '') === 'refresh';
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function createToken(int $userId, string $email, int $ttl, string $type): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $now = time();
        $payload = [
            'sub'  => $userId,
            'email' => $email,
            'type' => $type,
            'iat'  => $now,
            'exp'  => $now + $ttl,
        ];

        $encodedHeader  = $this->base64UrlEncode(json_encode($header));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));

        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "$encodedHeader.$encodedPayload", $this->secret, true)
        );

        return "$encodedHeader.$encodedPayload.$signature";
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

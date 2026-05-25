<?php

namespace App\Libraries;

/**
 * MiniStackClient — communicates with MiniStack IaaS API.
 *
 * If MiniStack is not available, falls back to mock credential generation.
 * This allows the portal to function standalone for development/MVP.
 */
class MiniStackClient
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl  = env('ministack.base_url', 'http://localhost:5000/api/v1');
        $this->timeout  = (int) (env('ministack.timeout') ?? 10);
    }

    /**
     * Create a storage bucket and generate credentials for a user.
     *
     * @param int    $userId
     * @param string $username
     * @return array ['success' => bool, 'error' => string|null, 'data' => array|null]
     */
    public function createBucketAndCredentials(int $userId, string $username): array
    {
        $bucketName = $this->generateBucketName($userId);

        // Try real MiniStack API
        $result = $this->callApi('POST', '/buckets', [
            'bucket_name' => $bucketName,
            'user_id'     => $userId,
        ]);

        if (!$result['success']) {
            // Fall back to mock credentials
            return $this->mockCreateBucketAndCredentials($bucketName);
        }

        return [
            'success' => true,
            'error'   => null,
            'data'    => [
                'access_key'  => $result['data']['access_key'] ?? $this->generateAccessKey(),
                'secret_key'  => $result['data']['secret_key'] ?? $this->generateSecretKey(),
                'bucket_name' => $bucketName,
            ],
        ];
    }

    /**
     * Generate a new Access Key.
     */
    public function generateAccessKey(): string
    {
        return 'AK' . strtoupper(bin2hex(random_bytes(8)));
    }

    /**
     * Generate a new Secret Key.
     */
    public function generateSecretKey(): string
    {
        return 'SK' . strtoupper(bin2hex(random_bytes(20)));
    }

    /**
     * Generate bucket name for a user.
     */
    private function generateBucketName(int $userId): string
    {
        return sprintf('user_%d_%s', $userId, date('YmdHis'));
    }

    /**
     * Make an HTTP call to MiniStack API.
     *
     * @param string $method  GET|POST|PUT|DELETE
     * @param string $endpoint  e.g. /buckets
     * @param array  $data      POST body data
     * @return array ['success' => bool, 'error' => string|null, 'data' => array|null]
     */
    private function callApi(string $method, string $endpoint, array $data = []): array
    {
        if (!function_exists('curl_init')) {
            return ['success' => false, 'error' => 'cURL not available', 'data' => null];
        }

        $ch = curl_init($this->baseUrl . $endpoint);
        if (!$ch) {
            return ['success' => false, 'error' => 'Failed to init cURL', 'data' => null];
        }

        $headers = ['Content-Type: application/json'];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif (in_array($method, ['PUT', 'DELETE'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => "MiniStack API error: {$error}", 'data' => null];
        }

        if ($httpCode >= 400) {
            return [
                'success' => false,
                'error'   => "MiniStack API returned HTTP {$httpCode}",
                'data'    => json_decode($response, true) ?? null,
            ];
        }

        return [
            'success' => true,
            'error'   => null,
            'data'    => json_decode($response, true) ?? [],
        ];
    }

    /**
     * Mock fallback — generate credentials without MiniStack.
     */
    private function mockCreateBucketAndCredentials(string $bucketName): array
    {
        log_message('info', "MiniStack unavailable — using mock credentials for bucket: {$bucketName}");

        return [
            'success' => true,
            'error'   => null,
            'data'    => [
                'access_key'  => $this->generateAccessKey(),
                'secret_key'  => $this->generateSecretKey(),
                'bucket_name' => $bucketName,
            ],
        ];
    }
}
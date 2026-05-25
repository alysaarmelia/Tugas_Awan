<?php

namespace App\Services;

use App\Models\UserCredentialModel;
use App\Models\ActivityLogModel;
use App\Libraries\MiniStackClient;

/**
 * CredentialsService — manages Access Key / Secret Key for users.
 */
class CredentialsService
{
    private UserCredentialModel $credentialModel;
    private ActivityLogModel $logModel;
    private MiniStackClient $miniStack;

    public function __construct()
    {
        $this->credentialModel = new UserCredentialModel();
        $this->logModel       = new ActivityLogModel();
        $this->miniStack      = new MiniStackClient();
    }

    /**
     * Get credentials for a user (masked).
     */
    public function getCredentials(int $userId): ?array
    {
        $cred = $this->credentialModel->findByUserId($userId);

        if (!$cred) {
            return null;
        }

        return [
            'access_key'        => $cred->getMaskedAccessKey(),
            'secret_key'        => $cred->getMaskedSecretKey(),
            'bucket_name'       => $cred->bucket_name,
            'created_at'        => $cred->created_at,
            'last_regenerated'  => $cred->last_regenerated,
        ];
    }

    /**
     * Get full (revealed) credentials for a user.
     */
    public function getRevealedCredentials(int $userId): ?array
    {
        $cred = $this->credentialModel->findByUserId($userId);

        if (!$cred) {
            return null;
        }

        return [
            'access_key'        => $cred->access_key,
            'secret_key'        => $cred->secret_key,
            'bucket_name'       => $cred->bucket_name,
            'created_at'        => $cred->created_at,
            'last_regenerated'  => $cred->last_regenerated,
        ];
    }

    /**
     * Create credentials for a new user (called during auto-provisioning).
     *
     * @return array ['success' => bool, 'error' => string|null, 'data' => array|null]
     */
    public function createCredentialsForUser(int $userId, string $username): array
    {
        // Check if already exists
        if ($this->credentialModel->findByUserId($userId)) {
            return ['success' => false, 'error' => 'Credentials already exist', 'data' => null];
        }

        // Try MiniStack first, fall back to mock
        $result = $this->miniStack->createBucketAndCredentials($userId, $username);

        if (!$result['success']) {
            return $result;
        }

        // Store in database
        $this->credentialModel->createCredentials(
            $userId,
            $result['data']['access_key'],
            $result['data']['secret_key'],
            $result['data']['bucket_name']
        );

        // Log
        $this->logModel->logAction(
            $userId,
            'credentials_generated',
            "Access Key and Secret Key generated via MiniStack",
            'completed'
        );

        return [
            'success' => true,
            'error'   => null,
            'data'    => [
                'access_key'  => $result['data']['access_key'],
                'secret_key'  => $result['data']['secret_key'],
                'bucket_name' => $result['data']['bucket_name'],
            ],
        ];
    }

    /**
     * Regenerate credentials for a user.
     *
     * @return array ['success' => bool, 'error' => string|null, 'data' => array|null]
     */
    public function regenerateCredentials(int $userId): array
    {
        $existing = $this->credentialModel->findByUserId($userId);
        if (!$existing) {
            return ['success' => false, 'error' => 'No credentials found', 'data' => null];
        }

        // Generate new keys (mock for now)
        $newAccessKey = $this->miniStack->generateAccessKey();
        $newSecretKey = $this->miniStack->generateSecretKey();

        // Update database
        $this->credentialModel->regenerateCredentials($userId, $newAccessKey, $newSecretKey);

        // Log
        $this->logModel->logAction(
            $userId,
            'credentials_regenerated',
            "Credentials regenerated. Old bucket retained: {$existing->bucket_name}",
            'completed'
        );

        return [
            'success' => true,
            'error'   => null,
            'data'    => [
                'access_key'  => $newAccessKey,
                'secret_key'  => $newSecretKey,
            ],
        ];
    }
}

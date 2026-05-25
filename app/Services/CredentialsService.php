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
     * Get credentials for a user.
     * The secret_key is NEVER returned after initial creation.
     * The access_key reveal is handled client-side with a timed session.
     *
     * @return array{
     *     access_key: string,
     *     masked_ak: string,
     *     masked_sk: string,
     *     bucket_name: string,
     *     created_at: string|null,
     *     last_regenerated: string|null,
     *     can_reveal_sk_until: int 0 if not revealed, > 0 = unix timestamp if within window
     * }
     */
    public function getCredentials(int $userId): ?array
    {
        $cred = $this->credentialModel->findByUserId($userId);

        if (!$cred) {
            return null;
        }

        return [
            'access_key'           => $cred->access_key,
            'secret_key'           => null, // Never expose secret key after page load
            'masked_ak'            => $cred->getMaskedAccessKey(),
            'masked_sk'            => $cred->getMaskedSecretKey(),
            'bucket_name'          => $cred->bucket_name,
            'created_at'           => $this->formatDateTime($cred->created_at),
            'last_regenerated'     => $this->formatDateTime($cred->last_regenerated),
            'can_reveal_sk_until'  => 0,
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
            'created_at'        => $this->formatDateTime($cred->created_at),
            'last_regenerated'  => $this->formatDateTime($cred->last_regenerated),
        ];
    }

    private function formatDateTime($value): ?string
    {
        if (!$value) return null;
        if (is_object($value) && method_exists($value, 'format')) return $value->format('Y-m-d H:i:s');
        return (string) $value;
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
     * The full secret_key is ONLY returned in the regenerate response.
     * It is the user's only chance to copy it — after this, the backend
     * will never return secret_key again until the next regen.
     *
     * @return array ['success' => bool, 'error' => string|null, 'data' => array|null]
     */
    public function regenerateCredentials(int $userId): array
    {
        $existing = $this->credentialModel->findByUserId($userId);
        if (!$existing) {
            return ['success' => false, 'error' => 'No credentials found', 'data' => null];
        }

        // Generate new keys
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

        // 10-second reveal window — full keys are ONLY returned here
        $revealExpiry = time() + 10;

        return [
            'success' => true,
            'error'   => null,
            'data'    => [
                'access_key'           => $newAccessKey,
                'secret_key'           => $newSecretKey,
                'masked_ak'            => $this->miniStack->maskAccessKey($newAccessKey),
                'masked_sk'            => $this->miniStack->maskSecretKey($newSecretKey),
                'can_reveal_sk_until' => $revealExpiry,
            ],
        ];
    }
}

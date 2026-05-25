<?php

namespace App\Models;

use App\Entities\UserCredential;
use CodeIgniter\Model;

class UserCredentialModel extends Model
{
    protected $table            = 'user_credentials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = UserCredential::class;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['user_id', 'access_key', 'secret_key', 'bucket_name', 'created_at', 'last_regenerated'];
    protected $useTimestamps    = false;

    /**
     * Get credentials by user ID.
     */
    public function findByUserId(int $userId): ?UserCredential
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Create credentials for a user.
     */
    public function createCredentials(
        int $userId,
        string $accessKey,
        string $secretKey,
        string $bucketName
    ): UserCredential {
        $credentials = new UserCredential([
            'user_id'   => $userId,
            'access_key' => $accessKey,
            'secret_key' => $secretKey,
            'bucket_name' => $bucketName,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->insert($credentials);
        $credentials->id = $this->insertID();

        return $credentials;
    }

    /**
     * Regenerate credentials for a user.
     */
    public function regenerateCredentials(int $userId, string $newAccessKey, string $newSecretKey): ?UserCredential
    {
        $existing = $this->findByUserId($userId);
        if (!$existing) {
            return null;
        }

        $this->update($existing->id, [
            'access_key'       => $newAccessKey,
            'secret_key'       => $newSecretKey,
            'last_regenerated' => date('Y-m-d H:i:s'),
        ]);

        return $this->find($existing->id);
    }
}
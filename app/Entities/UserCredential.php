<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserCredential extends Entity
{
    protected $casts = [
        'id'                => 'integer',
        'user_id'           => 'integer',
        'created_at'        => 'datetime',
        'last_regenerated'  => 'datetime',
    ];

    protected $dates = ['created_at', 'last_regenerated'];

    /**
     * Mask the access key for display.
     */
    public function getMaskedAccessKey(): string
    {
        if (strlen($this->access_key) <= 8) {
            return $this->access_key;
        }
        return 'AK' . str_repeat('••••••••', 6) . substr($this->access_key, -4);
    }

    /**
     * Mask the secret key for display.
     */
    public function getMaskedSecretKey(): string
    {
        if (strlen($this->secret_key) <= 8) {
            return $this->secret_key;
        }
        return 'SK' . str_repeat('••••••••', 4) . substr($this->secret_key, -3);
    }
}
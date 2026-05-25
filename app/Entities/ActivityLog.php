<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ActivityLog extends Entity
{
    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'created_at' => 'datetime',
    ];

    protected $dates = ['created_at'];

    public const ACTION_USER_REGISTERED          = 'user_registered';
    public const ACTION_SUBSCRIPTION_SELECTED    = 'subscription_selected';
    public const ACTION_SUBSCRIPTION_CHANGED      = 'subscription_changed';
    public const ACTION_STORAGE_RENTED           = 'storage_rented';
    public const ACTION_CREDENTIALS_GENERATED    = 'credentials_generated';
    public const ACTION_CREDENTIALS_REGENERATED  = 'credentials_regenerated';
    public const ACTION_LOGIN                    = 'login';
    public const ACTION_LOGOUT                   = 'logout';

    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED    = 'failed';
    public const STATUS_PENDING  = 'pending';

    /**
     * Get human-readable action label.
     */
    public function getActionLabel(): string
    {
        return match ($this->action) {
            self::ACTION_USER_REGISTERED         => 'Account Registered',
            self::ACTION_SUBSCRIPTION_SELECTED  => 'Subscription Selected',
            self::ACTION_SUBSCRIPTION_CHANGED    => 'Subscription Changed',
            self::ACTION_STORAGE_RENTED          => 'Storage Rented',
            self::ACTION_CREDENTIALS_GENERATED   => 'Credentials Generated',
            self::ACTION_CREDENTIALS_REGENERATED=> 'Credentials Regenerated',
            self::ACTION_LOGIN                   => 'User Login',
            self::ACTION_LOGOUT                  => 'User Logout',
            default                              => $this->action,
        };
    }
}
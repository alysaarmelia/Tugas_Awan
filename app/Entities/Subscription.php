<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Subscription extends Entity
{
    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    protected $dates = ['start_date', 'end_date'];

    /**
     * Get storage quota in GB for this tier.
     */
    public function getStorageQuotaGb(): int
    {
        return match ($this->tier) {
            'free'       => 1,
            'pro'        => 50,
            'enterprise' => 500,
            default      => 1,
        };
    }

    /**
     * Get monthly price for this tier.
     */
    public function getMonthlyPrice(): float
    {
        return match ($this->tier) {
            'free'       => 0.00,
            'pro'        => 9.99,
            'enterprise' => 49.99,
            default      => 0.00,
        };
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
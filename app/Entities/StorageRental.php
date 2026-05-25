<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class StorageRental extends Entity
{
    protected $casts = [
        'id'          => 'integer',
        'user_id'     => 'integer',
        'gb_amount'   => 'integer',
        'price_per_gb' => 'float',
        'created_at'  => 'datetime',
    ];

    protected $dates = ['created_at'];

    /**
     * Get total cost of this rental.
     */
    public function getTotalCost(): float
    {
        return (float) $this->gb_amount * (float) $this->price_per_gb;
    }
}
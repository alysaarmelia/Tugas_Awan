<?php

namespace App\Models;

use App\Entities\StorageRental;
use CodeIgniter\Model;

class StorageRentalModel extends Model
{
    protected $table            = 'storage_rentals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = StorageRental::class;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['user_id', 'gb_amount', 'price_per_gb', 'created_at'];
    protected $useTimestamps    = false;

    /**
     * Get all rentals for a user.
     */
    public function findByUserId(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }

    /**
     * Get total rented storage for a user (sum of all rentals).
     */
    public function getTotalRentedForUser(int $userId): int
    {
        $result = $this->selectSum('gb_amount')
            ->where('user_id', $userId)
            ->first();

        return (int) ($result->gb_amount ?? 0);
    }

    /**
     * Create a storage rental.
     */
    public function createRental(int $userId, int $gbAmount, float $pricePerGb = 0.10): StorageRental
    {
        $rental = new StorageRental([
            'user_id'     => $userId,
            'gb_amount'   => $gbAmount,
            'price_per_gb' => $pricePerGb,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->insert($rental);
        $rental->id = $this->insertID();

        return $rental;
    }
}
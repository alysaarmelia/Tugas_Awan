<?php

namespace App\Services;

use App\Models\StorageRentalModel;
use App\Models\SubscriptionModel;
use App\Models\ActivityLogModel;

/**
 * StorageService — handles storage quota calculation and rental.
 */
class StorageService
{
    private StorageRentalModel $rentalModel;
    private SubscriptionModel $subscriptionModel;
    private ActivityLogModel $logModel;
    private UserService $userService;

    public const MAX_RENTAL_GB   = 100;
    public const PRICE_PER_GB     = 0.10;
    public const SIMULATED_USAGE_GB = 0.0; // Simulated for MVP

    public function __construct()
    {
        $this->rentalModel        = new StorageRentalModel();
        $this->subscriptionModel  = new SubscriptionModel();
        $this->logModel           = new ActivityLogModel();
        $this->userService        = new UserService();
    }

    /**
     * Get storage quota and usage for a user.
     */
    public function getStorageQuota(int $userId): array
    {
        $subscription = $this->subscriptionModel->findByUserId($userId);
        $baseQuotaGb  = $subscription
            ? (UserService::TIER_QUOTAS[$subscription->tier] ?? 1)
            : 1;

        $rentedGb    = $this->rentalModel->getTotalRentedForUser($userId);
        $totalQuotaGb = $baseQuotaGb + $rentedGb;
        $usedGb      = self::SIMULATED_USAGE_GB;
        $remainingGb = max(0, $totalQuotaGb - $usedGb);
        $usagePercent = $totalQuotaGb > 0
            ? round(($usedGb / $totalQuotaGb) * 100, 2)
            : 0;

        $tier = $subscription?->tier ?? 'free';

        return [
            'base_quota_gb'   => $baseQuotaGb,
            'rented_gb'       => $rentedGb,
            'total_quota_gb'  => $totalQuotaGb,
            'used_gb'         => $usedGb,
            'remaining_gb'    => $remainingGb,
            'usage_percent'   => $usagePercent,
            'subscription_tier' => $tier,
        ];
    }

    /**
     * Rent additional storage for a user.
     *
     * @param int $userId
     * @param int $amountGb Amount to rent (1-100)
     * @return array ['success' => bool, 'error' => string|null, 'data' => array|null]
     */
    public function rentStorage(int $userId, int $amountGb): array
    {
        // Validate amount
        if ($amountGb < 1 || $amountGb > self::MAX_RENTAL_GB) {
            return [
                'success' => false,
                'error'   => "Rental amount must be between 1 and " . self::MAX_RENTAL_GB . " GB",
                'data'    => null,
            ];
        }

        // Check if user has subscription — auto-create free if missing
        if (!$this->subscriptionModel->findByUserId($userId)) {
            $this->subscriptionModel->insert([
                'user_id'    => $userId,
                'tier'       => 'free',
                'status'     => 'active',
                'start_date' => date('Y-m-d H:i:s'),
            ]);
        }

        // Create rental
        $rental = $this->rentalModel->createRental($userId, $amountGb, self::PRICE_PER_GB);
        $cost   = $rental->getTotalCost();

        // Log action
        $this->logModel->logAction(
            $userId,
            'storage_rented',
            "Rented {$amountGb} GB additional storage at $" . self::PRICE_PER_GB . "/GB",
            'completed'
        );

        // Return updated quota
        $newQuota = $this->getStorageQuota($userId);

        return [
            'success' => true,
            'error'   => null,
            'data'    => [
                'rental_id'          => $rental->id,
                'amount_gb'          => $amountGb,
                'cost_usd'           => $cost,
                'new_total_quota_gb' => $newQuota['total_quota_gb'],
            ],
        ];
    }

    /**
     * Get all rentals for a user.
     */
    public function getRentals(int $userId): array
    {
        $rentals = $this->rentalModel->findByUserId($userId);
        return array_map(fn($r) => [
            'id'          => $r->id,
            'gb_amount'   => $r->gb_amount,
            'price_per_gb'=> $r->price_per_gb,
            'created_at'  => is_object($r->created_at)
                ? $r->created_at->format('Y-m-d H:i:s')
                : $r->created_at,
        ], $rentals);
    }
}

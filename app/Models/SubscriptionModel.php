<?php

namespace App\Models;

use App\Entities\Subscription;
use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table            = 'subscriptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Subscription::class;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['user_id', 'tier', 'status', 'start_date', 'end_date'];
    protected $useTimestamps    = false;

    /**
     * Get subscription by user ID.
     */
    public function findByUserId(int $userId): ?Subscription
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Create or update subscription for user.
     */
    public function setSubscriptionForUser(int $userId, string $tier): Subscription
    {
        $existing = $this->findByUserId($userId);

        if ($existing) {
            // Archive old subscription by setting status to cancelled
            $this->update($existing->id, ['status' => 'cancelled']);
        }

        $subscription = new Subscription([
            'user_id'    => $userId,
            'tier'       => $tier,
            'status'     => 'active',
            'start_date' => date('Y-m-d H:i:s'),
        ]);

        $this->insert($subscription);
        $subscription->id = $this->insertID();

        return $subscription;
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(int $userId): bool
    {
        $sub = $this->findByUserId($userId);
        return $sub && $sub->status === 'active';
    }
}
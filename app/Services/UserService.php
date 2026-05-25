<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\SubscriptionModel;
use App\Models\ActivityLogModel;

/**
 * UserService — handles user profile and subscription management.
 */
class UserService
{
    private UserModel $userModel;
    private SubscriptionModel $subscriptionModel;
    private ActivityLogModel $logModel;

    public const TIER_FREE       = 'free';
    public const TIER_PRO        = 'pro';
    public const TIER_ENTERPRISE = 'enterprise';

    public const TIER_QUOTAS = [
        'free'       => 1,
        'pro'        => 50,
        'enterprise' => 500,
    ];

    public const TIER_PRICES = [
        'free'       => 0.00,
        'pro'        => 9.99,
        'enterprise' => 49.99,
    ];

    public const VALID_TIERS = ['free', 'pro', 'enterprise'];

    public function __construct()
    {
        $this->userModel         = new UserModel();
        $this->subscriptionModel = new SubscriptionModel();
        $this->logModel         = new ActivityLogModel();
    }

    /**
     * Get user by ID.
     */
    public function getUserById(int $userId): ?object
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return null;
        }

        return (object) [
            'id'         => $user->id,
            'username'   => $user->username,
            'email'      => $user->email,
            'created_at' => $user->created_at,
        ];
    }

    /**
     * Get user's subscription details.
     */
    public function getSubscription(int $userId): ?array
    {
        $subscription = $this->subscriptionModel->findByUserId($userId);

        if (!$subscription) {
            return null;
        }

        return [
            'tier'        => $subscription->tier,
            'status'      => $subscription->status,
            'start_date'  => $subscription->start_date,
            'end_date'    => $subscription->end_date,
            'quota_gb'    => self::TIER_QUOTAS[$subscription->tier] ?? 1,
            'price_usd'   => self::TIER_PRICES[$subscription->tier] ?? 0.00,
        ];
    }

    /**
     * Set or change subscription tier for user.
     *
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function setSubscription(int $userId, string $tier): array
    {
        if (!in_array($tier, self::VALID_TIERS, true)) {
            return ['success' => false, 'error' => 'Invalid subscription tier'];
        }

        $existing = $this->subscriptionModel->findByUserId($userId);
        $isNew    = !$existing || $existing->status !== 'active';

        $this->subscriptionModel->setSubscriptionForUser($userId, $tier);

        $action = $isNew ? 'subscription_selected' : 'subscription_changed';
        $details = $isNew
            ? "Selected {$tier} subscription ({$tier} GB)"
            : "Changed subscription to {$tier} ({$tier} GB)";

        $this->logModel->logAction($userId, $action, $details, 'completed');

        return ['success' => true, 'error' => null];
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(int $userId): bool
    {
        return $this->subscriptionModel->hasActiveSubscription($userId);
    }

    /**
     * Get all available tiers with details.
     */
    public function getAvailableTiers(): array
    {
        return [
            [
                'tier'       => 'free',
                'name'       => 'Free',
                'quota_gb'   => 1,
                'price_usd'  => 0.00,
                'features'   => ['Basic dashboard', 'Activity logs'],
            ],
            [
                'tier'       => 'pro',
                'name'       => 'Pro',
                'quota_gb'   => 50,
                'price_usd'  => 9.99,
                'features'   => ['+ Credential management', '+ Priority support'],
            ],
            [
                'tier'       => 'enterprise',
                'name'       => 'Enterprise',
                'quota_gb'   => 500,
                'price_usd'  => 49.99,
                'features'   => ['+ Bulk storage rental', '+ Full API access'],
            ],
        ];
    }
}

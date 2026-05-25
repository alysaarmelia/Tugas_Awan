<?php

namespace App\Services;

use App\Models\ActivityLogModel;

/**
 * LoggingService — handles activity log retrieval and management.
 */
class LoggingService
{
    private ActivityLogModel $logModel;

    public const DEFAULT_PAGE  = 1;
    public const DEFAULT_LIMIT  = 20;
    public const MAX_LIMIT      = 100;

    public const VALID_ACTION_TYPES = [
        'user_registered',
        'subscription_selected',
        'subscription_changed',
        'storage_rented',
        'credentials_generated',
        'credentials_regenerated',
        'login',
        'logout',
    ];

    public function __construct()
    {
        $this->logModel = new ActivityLogModel();
    }

    /**
     * Get paginated activity logs for a user.
     */
    public function getLogsForUser(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $limit = self::DEFAULT_LIMIT,
        ?string $actionType = null
    ): array {
        // Clamp values
        $page  = max(1, $page);
        $limit = min(max(1, $limit), self::MAX_LIMIT);

        $result = $this->logModel->getPaginatedLogsForUser($userId, $page, $limit, $actionType);

        $logs = array_map(function ($log) {
            return [
                'id'         => $log->id,
                'action'     => $log->action,
                'action_label' => $log->getActionLabel(),
                'details'    => $log->details,
                'status'     => $log->status,
                'created_at' => $log->created_at,
            ];
        }, $result['logs']);

        return [
            'logs'       => $logs,
            'pagination' => [
                'page'       => $page,
                'limit'      => $limit,
                'total'      => $result['total'],
                'total_pages'=> $result['total_pages'],
            ],
        ];
    }

    /**
     * Log a custom action.
     */
    public function logAction(
        int $userId,
        string $action,
        ?string $details = null,
        string $status = 'completed'
    ): void {
        $this->logModel->logAction($userId, $action, $details, $status);
    }

    /**
     * Get available action types for filtering.
     */
    public function getActionTypes(): array
    {
        $labels = [
            'user_registered'          => 'Account Registered',
            'subscription_selected'   => 'Subscription Selected',
            'subscription_changed'    => 'Subscription Changed',
            'storage_rented'         => 'Storage Rented',
            'credentials_generated'   => 'Credentials Generated',
            'credentials_regenerated' => 'Credentials Regenerated',
            'login'                   => 'Login',
            'logout'                  => 'Logout',
        ];

        $types = [];
        foreach (self::VALID_ACTION_TYPES as $type) {
            $types[] = [
                'value' => $type,
                'label' => $labels[$type] ?? $type,
            ];
        }

        return $types;
    }
}

<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseApiController;
use App\Services\LoggingService;

/**
 * LogsController — activity log retrieval with pagination.
 * Base: /api/v1/logs
 */
class LogsController extends BaseApiController
{
    private LoggingService $loggingService;

    public function __construct()
    {
        $this->loggingService = service('loggingService');
    }

    /**
     * GET /api/v1/logs
     * Query params: ?page=1&limit=20&action_type=rental
     */
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        $page       = (int) ($this->request->getGet('page') ?? 1);
        $limit      = min((int) ($this->request->getGet('limit') ?? 20), LoggingService::MAX_LIMIT);
        $actionType = $this->request->getGet('action_type');

        // Validate action_type if provided
        if ($actionType && !in_array($actionType, LoggingService::VALID_ACTION_TYPES, true)) {
            return $this->validationError([
                'action_type' => 'Invalid action_type filter.',
            ]);
        }

        $result = $this->loggingService->getLogsForUser($userId, $page, $limit, $actionType);

        return $this->paginated(
            $result['logs'],
            $page,
            $limit,
            $result['pagination']['total'],
            'Activity logs retrieved.'
        );
    }
}

<?php

namespace App\Models;

use App\Entities\ActivityLog;
use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = ActivityLog::class;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['user_id', 'action', 'details', 'status', 'created_at'];
    protected $useTimestamps    = false;

    /**
     * Get paginated logs for a user.
     */
    public function getPaginatedLogsForUser(
        int $userId,
        int $page = 1,
        int $limit = 20,
        ?string $actionType = null
    ): array {
        $builder = $this->builder();
        $builder->where('user_id', $userId);

        if ($actionType) {
            $builder->where('action', $actionType);
        }

        $builder->orderBy('created_at', 'DESC');

        $total = $builder->countAllResults();
        $totalPages = (int) ceil($total / $limit);

        // Use findAll() to respect returnType = ActivityLog::class
        $offset    = ($page - 1) * $limit;
        $logs      = $this->findAll($limit, $offset);
        // Re-apply the same filters since findAll doesn't use the builder above
        $filteredLogs = array_filter($logs, function ($log) use ($userId, $actionType) {
            if ($log->user_id !== $userId) return false;
            if ($actionType && $log->action !== $actionType) return false;
            return true;
        });

        return [
            'logs'       => array_values($filteredLogs),
            'total'      => $total,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * Log an action for a user.
     */
    public function logAction(
        int $userId,
        string $action,
        ?string $details = null,
        string $status = 'completed'
    ): ActivityLog {
        $log = new ActivityLog([
            'user_id'    => $userId,
            'action'     => $action,
            'details'    => $details,
            'status'     => $status,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->insert($log);
        $log->id = $this->insertID();

        return $log;
    }

    /**
     * Get recent logs for a user.
     */
    public function getRecentLogsForUser(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }
}
<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseApiController;
use App\Services\StorageService;

/**
 * StorageController — storage quota and rental.
 * Base: /api/v1/storage
 */
class StorageController extends BaseApiController
{
    private StorageService $storageService;

    public function __construct()
    {
        $this->storageService = service('storageService');
    }

    /**
     * GET /api/v1/storage
     */
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        return $this->success($this->storageService->getStorageQuota($userId));
    }

    /**
     * POST /api/v1/storage/rent
     * Body: { "amount_gb": int }
     */
    public function rent(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        $body = $this->getBody();

        if (!$this->validate([
            'amount_gb' => 'required|integer|greater_than[0]|less_than_equal_to[100]',
        ])) {
            return $this->validationError($this->validator->getErrors());
        }

        $result = $this->storageService->rentStorage($userId, (int) $body['amount_gb']);

        if (!$result['success']) {
            return $this->validationError(['amount_gb' => $result['error']]);
        }

        return $this->success([
            'message'            => 'Storage rental successful.',
            'amount_gb'          => $result['data']['amount_gb'],
            'cost_usd'           => round($result['data']['cost_usd'], 2),
            'new_total_quota_gb' => $result['data']['new_total_quota_gb'],
        ]);
    }

    /**
     * GET /api/v1/storage/rentals
     */
    public function rentals(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        return $this->success($this->storageService->getRentals($userId));
    }
}

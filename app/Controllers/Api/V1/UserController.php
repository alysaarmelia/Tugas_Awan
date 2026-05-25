<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseApiController;
use App\Services\UserService;

/**
 * UserController — user profile and subscription management.
 * Base: /api/v1/user
 */
class UserController extends BaseApiController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = service('userService');
    }

    // ================================================================
    // AUTHENTICATED
    // ================================================================

    /**
     * GET /api/v1/user/me
     */
    public function me(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        $user = $this->userService->getUserById($userId);

        if (!$user) {
            return $this->notFound('User not found.');
        }

        return $this->success($user, 'User profile.');
    }

    /**
     * GET /api/v1/user/subscription
     */
    public function getSubscription(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        return $this->success($this->userService->getSubscription($userId));
    }

    /**
     * POST /api/v1/user/subscription
     * Body: { "tier": "free|pro|enterprise" }
     */
    public function setSubscription(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        $body = $this->getBody();

        if (!$this->validate(['tier' => 'required|in_list[free,pro,enterprise]'])) {
            return $this->validationError($this->validator->getErrors());
        }

        $result = $this->userService->setSubscription($userId, $body['tier']);

        if (!$result['success']) {
            return $this->error($result['error'], 422);
        }

        return $this->success([
            'message'      => 'Subscription set successfully.',
            'subscription' => $this->userService->getSubscription($userId),
        ]);
    }

    /**
     * GET /api/v1/user/subscription/tiers
     */
    public function getTiers(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->success(['tiers' => $this->userService->getAvailableTiers()]);
    }
}

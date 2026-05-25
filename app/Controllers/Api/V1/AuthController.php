<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseApiController;
use App\Services\AuthService;
use App\Services\CredentialsService;

/**
 * AuthController — public & authenticated auth endpoints.
 * Base: /api/v1/auth
 */
class AuthController extends BaseApiController
{
    private AuthService $authService;
    private CredentialsService $credentialsService;

    public function __construct()
    {
        $this->authService        = service('authService');
        $this->credentialsService = service('credentialsService');
    }

    // ================================================================
    // PUBLIC — no auth required
    // ================================================================

    /**
     * POST /api/v1/auth/register
     */
    public function register(): \CodeIgniter\HTTP\ResponseInterface
    {
        $body = $this->getBody();

        $rules = [
            'username'        => 'required|min_length[3]|max_length[50]',
            'email'           => 'required|valid_email',
            'password'        => 'required|min_length[8]',
            'confirm_password'=> 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        // Strength check
        $pwErrors = $this->authService->validatePasswordStrength($body['password']);
        if ($pwErrors) {
            return $this->validationError(['password' => implode('; ', $pwErrors)]);
        }

        $result = $this->authService->register(
            trim($body['username']),
            trim($body['email']),
            $body['password']
        );

        if (!$result['success']) {
            return $this->conflict($result['error']);
        }

        // Auto-provision MiniStack credentials
        $this->credentialsService->createCredentialsForUser($result['user_id'], trim($body['username']));

        return $this->created(['user_id' => $result['user_id']], 'Registration successful.');
    }

    /**
     * POST /api/v1/auth/login
     */
    public function login(): \CodeIgniter\HTTP\ResponseInterface
    {
        $body = $this->getBody();

        if (!$this->validate([
            'email'    => 'required|valid_email',
            'password' => 'required',
        ])) {
            return $this->validationError($this->validator->getErrors());
        }

        $tokens = $this->authService->login($body['email'], $body['password']);

        if (!$tokens) {
            return $this->unauthorized('Invalid email or password.');
        }

        return $this->success($tokens, 'Login successful.');
    }

    /**
     * POST /api/v1/auth/refresh
     */
    public function refresh(): \CodeIgniter\HTTP\ResponseInterface
    {
        $body = $this->getBody();

        if (!$this->validate(['refresh_token' => 'required'])) {
            return $this->validationError($this->validator->getErrors());
        }

        $tokens = $this->authService->refreshAccessToken($body['refresh_token']);

        if (!$tokens) {
            return $this->unauthorized('Invalid or expired refresh token.');
        }

        return $this->success($tokens, 'Token refreshed.');
    }

    // ================================================================
    // AUTHENTICATED — auth required
    // ================================================================

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        $this->authService->logout($userId);
        return $this->success(null, 'Logged out successfully.');
    }
}

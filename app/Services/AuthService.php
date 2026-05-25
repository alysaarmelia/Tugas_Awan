<?php

namespace App\Services;

use App\Libraries\JwtLibrary;
use App\Models\UserModel;
use App\Models\ActivityLogModel;
use App\Models\SubscriptionModel;

/**
 * AuthService — handles user registration, login, logout, and token management.
 */
class AuthService
{
    private UserModel $userModel;
    private ActivityLogModel $logModel;
    private SubscriptionModel $subscriptionModel;
    private JwtLibrary $jwt;

    public function __construct()
    {
        $this->userModel         = new UserModel();
        $this->logModel          = new ActivityLogModel();
        $this->subscriptionModel = new SubscriptionModel();
        $this->jwt               = new JwtLibrary();
    }

    /**
     * Register a new user.
     *
     * @param string $username
     * @param string $email
     * @param string $password  Plain-text password (will be hashed)
     * @return array ['success' => bool, 'user_id' => int|null, 'error' => string|null]
     */
    public function register(string $username, string $email, string $password): array
    {
        // Validate email uniqueness
        if ($this->userModel->findByEmail($email)) {
            return ['success' => false, 'user_id' => null, 'error' => 'Email already registered'];
        }

        if ($this->userModel->findByUsername($username)) {
            return ['success' => false, 'user_id' => null, 'error' => 'Username already taken'];
        }

        // Create user
        $userId = $this->userModel->insert([
            'username'     => $username,
            'email'        => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        // Auto-create free subscription so users can rent storage immediately
        $this->subscriptionModel->insert([
            'user_id'    => $userId,
            'tier'       => 'free',
            'status'     => 'active',
            'start_date' => date('Y-m-d H:i:s'),
        ]);

        // Log registration
        $this->logModel->logAction(
            $userId,
            'user_registered',
            "Account created for {$username} ({$email})",
            'completed'
        );

        return ['success' => true, 'user_id' => $userId, 'error' => null];
    }

    /**
     * Authenticate a user and return JWT tokens.
     *
     * @return array|null Tokens array on success, null on failure
     */
    public function login(string $email, string $password): ?array
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user->password_hash)) {
            return null;
        }

        // Generate JWT tokens
        $tokens = $this->jwt->generateTokenPair($user->id, $user->email);

        // Log login
        $this->logModel->logAction(
            $user->id,
            'login',
            "User logged in from IP",
            'completed'
        );

        return $tokens;
    }

    /**
     * Validate an access token and return the user ID.
     */
    public function validateAccessToken(string $token): ?int
    {
        return $this->jwt->getUserIdFromToken($token);
    }

    /**
     * Refresh an access token using a refresh token.
     */
    public function refreshAccessToken(string $refreshToken): ?array
    {
        if (!$this->jwt->isRefreshToken($refreshToken)) {
            return null;
        }

        $payload = $this->jwt->validateToken($refreshToken);
        if (!$payload) {
            return null;
        }

        $userId  = (int) $payload['sub'];
        $email   = $payload['email'] ?? '';
        $user    = $this->userModel->find($userId);

        if (!$user) {
            return null;
        }

        return $this->jwt->generateTokenPair($user->id, $user->email);
    }

    /**
     * Logout — log the action.
     */
    public function logout(int $userId): void
    {
        $this->logModel->logAction($userId, 'logout', 'User logged out', 'completed');
    }

    /**
     * Validate password strength.
     * Min 8 chars, 1 uppercase, 1 number.
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least 1 uppercase letter';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least 1 number';
        }

        return $errors;
    }
}

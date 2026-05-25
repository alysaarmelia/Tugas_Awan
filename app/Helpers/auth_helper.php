<?php

namespace App\Helpers;

use App\Services\AuthService;

/**
 * Auth Helper — utility functions available in views.
 */
if (!function_exists('is_authenticated')) {
    /**
     * Check if the current request has a valid JWT.
     */
    function is_authenticated(): bool
    {
        $header = service('request')->getHeaderLine('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return false;
        }

        $token = trim(substr($header, 7));
        $jwt   = service('jwt');

        return $jwt->getUserIdFromToken($token) !== null;
    }
}

if (!function_exists('get_current_user_id')) {
    /**
     * Get the current authenticated user ID, or null.
     */
    function get_current_user_id(): ?int
    {
        $header = service('request')->getHeaderLine('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($header, 7));
        return service('jwt')->getUserIdFromToken($token);
    }
}
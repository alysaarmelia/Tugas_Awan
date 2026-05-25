<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * BaseApiController — base for all API controllers.
 *
 * Provides:
 * - Consistent JSON response envelope: { status, message, data, errors }
 * - Auth helpers: requireAuth(), apiUser()
 * - Request body parsing: getBody()
 * - Success/error response shortcuts
 */
class BaseApiController extends Controller
{
    protected $format = 'json';

    // ==============================================================
    // REQUEST HELPERS
    // ==============================================================

    /**
     * Parse JSON body. Always returns array (never null).
     */
    protected function getBody(): array
    {
        $body = $this->request->getJSON(true);
        return is_array($body) ? $body : [];
    }

    // ==============================================================
    // AUTH HELPERS
    // ==============================================================

    /**
     * Require JWT auth. Returns authenticated user ID or a 401 response.
     * Caller MUST check: `if ($userId instanceof ResponseInterface) return $userId;`
     */
    protected function requireAuth(): int|ResponseInterface
    {
        $header = $this->request->getHeaderLine('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return $this->unauthorized('Missing Authorization header.');
        }

        $token  = trim(substr($header, 7));
        $userId = service('jwt')->getUserIdFromToken($token);

        if (!$userId) {
            return $this->unauthorized('Invalid or expired token.');
        }

        // Ensure it is an access token, not refresh
        $payload = service('jwt')->validateToken($token);
        if (!$payload || ($payload['type'] ?? '') !== 'access') {
            return $this->unauthorized('Invalid token type.');
        }

        return (int) $userId;
    }

    /**
     * Get current authenticated user ID, or null (no error response sent).
     */
    protected function apiUserId(): ?int
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }
        return service('jwt')->getUserIdFromToken(trim(substr($header, 7)));
    }

    // ==============================================================
    // SUCCESS RESPONSES
    // ==============================================================

    /**
     * Generic success response.
     */
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($status)
            ->setJSON([
                'status'  => 'success',
                'message' => $message,
                'data'    => $data,
                'errors'  => null,
            ]);
    }

    /**
     * 201 Created.
     */
    protected function created(mixed $data = null, string $message = 'Created.'): ResponseInterface
    {
        return $this->success($data, $message, 201);
    }

    /**
     * 204 No Content.
     */
    protected function noContent(): ResponseInterface
    {
        return $this->response->setStatusCode(204);
    }

    /**
     * Paginated list response with meta.
     */
    protected function paginated(
        array $data,
        int $currentPage,
        int $perPage,
        int $total,
        string $message = 'OK'
    ): ResponseInterface {
        $pageCount = $perPage > 0 ? (int) ceil($total / $perPage) : 0;

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
            'meta'    => [
                'current_page' => $currentPage,
                'per_page'     => $perPage,
                'total'        => $total,
                'page_count'   => $pageCount,
            ],
        ]);
    }

    // ==============================================================
    // ERROR RESPONSES
    // ==============================================================

    /**
     * Generic error response.
     */
    protected function error(string $message, int $status = 400, mixed $errors = null): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON([
            'status'  => 'error',
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
        ]);
    }

    /**
     * 422 — Validation errors.
     */
    protected function validationError(array $errors, string $message = 'Validation failed.'): ResponseInterface
    {
        return $this->error($message, 422, $errors);
    }

    /**
     * 404 — Not found.
     */
    protected function notFound(string $message = 'Not found.'): ResponseInterface
    {
        return $this->error($message, 404);
    }

    /**
     * 403 — Forbidden.
     */
    protected function forbidden(string $message = 'Forbidden.'): ResponseInterface
    {
        return $this->error($message, 403);
    }

    /**
     * 401 — Unauthorized.
     */
    protected function unauthorized(string $message = 'Unauthorized.'): ResponseInterface
    {
        return $this->error($message, 401);
    }

    /**
     * 409 — Conflict (duplicate, state conflict).
     */
    protected function conflict(string $message = 'Conflict.'): ResponseInterface
    {
        return $this->error($message, 409);
    }

    /**
     * 500 — Server error.
     */
    protected function serverError(string $message = 'Server error.'): ResponseInterface
    {
        return $this->error($message, 500);
    }
}
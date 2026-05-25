<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * JwtAuthFilter — validates Bearer JWT tokens on /api/* routes.
 *
 * Registered as 'jwt' in app/Config/Filters.php.
 * Applied via route filter option: 'filter' => 'jwt'
 */
class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return $this->jsonError(401, 'Missing Authorization header.');
        }

        $token   = trim(substr($header, 7));
        $jwt     = service('jwt');
        $userId  = $jwt->getUserIdFromToken($token);

        if (!$userId) {
            return $this->jsonError(401, 'Invalid or expired token.');
        }

        $payload = $jwt->validateToken($token);
        if (!$payload || ($payload['type'] ?? '') !== 'access') {
            return $this->jsonError(401, 'Invalid token type.');
        }

        // Store user ID in request for downstream use
        $request->userId = (int) $userId;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        // Nothing to do after
    }

    private function jsonError(int $status, string $message): ResponseInterface
    {
        return service('response')
            ->setStatusCode($status)
            ->setJSON([
                'status'  => 'error',
                'message' => $message,
                'data'    => null,
                'errors'  => null,
            ]);
    }
}
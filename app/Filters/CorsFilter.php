<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CorsFilter — handles CORS preflight + adds CORS headers to responses.
 *
 * Registered as 'cors' in app/Config/Filters.php.
 * Apply to route groups that need cross-origin access.
 */
class CorsFilter implements FilterInterface
{
    private const ALLOWED_ORIGINS  = ['*'];
    private const ALLOWED_METHODS  = 'GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD';
    private const ALLOWED_HEADERS  = 'Authorization, Content-Type, X-Requested-With';

    public function before(RequestInterface $request, $arguments = null)
    {
        // Handle preflight (OPTIONS) — respond immediately, don't reach controller
        if ($request->getMethod() === 'options') {
            return $this->buildResponse(204, $request);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        return $this->buildResponse(200, $request, $response);
    }

    private function buildResponse(int $status, RequestInterface $request, ?ResponseInterface $response = null): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');
        $res    = service('response')->setStatusCode($status)
            ->setHeader('Access-Control-Allow-Origin', $origin ?: '*')
            ->setHeader('Access-Control-Allow-Credentials', 'true')
            ->setHeader('Access-Control-Allow-Methods', self::ALLOWED_METHODS)
            ->setHeader('Access-Control-Allow-Headers', self::ALLOWED_HEADERS)
            ->setHeader('Access-Control-Max-Age', '86400'); // 24 hours

        return $res;
    }
}
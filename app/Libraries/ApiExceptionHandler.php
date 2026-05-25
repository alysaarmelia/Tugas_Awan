<?php

namespace App\Libraries;

use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

/**
 * ApiExceptionHandler — returns consistent JSON error envelope
 * for all exceptions thrown on /api/* routes.
 *
 * Registered in app/Config/Exceptions.php via handler() override.
 */
class ApiExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode,
    ): void {
        $uri = $request->getUri()->getPath();

        // Only handle API routes
        if (!str_starts_with($uri, 'api/')) {
            parent::handle($exception, $request, $response, $statusCode, $exitCode);
            return;
        }

        // Determine status code from exception type
        if ($exception instanceof \CodeIgniter\Exceptions\PageNotFoundException) {
            $statusCode = 404;
        } elseif ($exception instanceof \CodeIgniter\Validation\ValidationException) {
            $statusCode = 422;
        }

        $message = ENVIRONMENT === 'production'
            ? 'An unexpected error occurred.'
            : $exception->getMessage();

        $body = [
            'status'  => 'error',
            'message' => $message,
            'data'    => null,
            'errors'  => null,
        ];

        // Include debug info in development only
        if (ENVIRONMENT !== 'production') {
            $body['debug'] = [
                'exception' => get_class($exception),
                'file'       => $exception->getFile(),
                'line'       => $exception->getLine(),
                'trace'      => $exception->getTraceAsString(),
            ];
        }

        $response->setStatusCode($statusCode)
            ->setJSON($body)
            ->send();

        exit($exitCode);
    }
}
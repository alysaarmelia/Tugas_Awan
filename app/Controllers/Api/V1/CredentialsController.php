<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseApiController;
use App\Services\CredentialsService;

/**
 * CredentialsController — Access Key / Secret Key management.
 * Base: /api/v1/credentials
 */
class CredentialsController extends BaseApiController
{
    private CredentialsService $credentialsService;

    public function __construct()
    {
        $this->credentialsService = service('credentialsService');
    }

    /**
     * GET /api/v1/credentials
     * Returns masked credentials. secret_key is always null — only revealed
     * transiently via the regenerate endpoint on the moment of creation.
     */
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        $credentials = $this->credentialsService->getCredentials($userId);

        if (!$credentials) {
            return $this->notFound('Credentials not found.');
        }

        return $this->success($credentials);
    }

    /**
     * POST /api/v1/credentials/regenerate
     * Regenerates Access Key + Secret Key. Full keys are ONLY returned here.
     * The secret_key cannot be retrieved again — only via next regen.
     */
    public function regenerate(): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->requireAuth();
        if ($userId instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $userId;
        }

        $result = $this->credentialsService->regenerateCredentials($userId);

        if (!$result['success']) {
            return $this->error($result['error'], 422);
        }

        return $this->success([
            'message'              => 'Credentials regenerated successfully.',
            'access_key'           => $result['data']['access_key'],
            'secret_key'           => $result['data']['secret_key'],
            'masked_ak'            => $result['data']['masked_ak'],
            'masked_sk'            => $result['data']['masked_sk'],
            'can_reveal_sk_until' => $result['data']['can_reveal_sk_until'],
        ]);
    }
}

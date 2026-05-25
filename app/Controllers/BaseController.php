<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController — base for all web (non-API) controllers.
 *
 * Provides request/response access. Heavy auth work is handled by
 * filter classes; keep this lean for page controllers.
 */
abstract class BaseController extends Controller
{
    protected $helpers = ['auth', 'url'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
    }
}
<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ================================================================
// FRONTEND ROUTES
// ================================================================

$routes->get('/', 'Home::index', ['as' => 'home']);
$routes->get('/dashboard',   'Home::dashboard',   ['as' => 'dashboard']);
$routes->get('/storage',     'Home::storage',     ['as' => 'storage']);
$routes->get('/credentials', 'Home::credentials', ['as' => 'credentials']);
$routes->get('/subscription','Home::subscription',['as' => 'subscription']);
$routes->get('/logs',        'Home::logs',        ['as' => 'logs']);

// ================================================================
// API ROUTES (versioned: /api/v1)
// ================================================================

$routes->group('api/v1', [
    'namespace' => 'App\Controllers\Api\V1',
], function ($routes) {

    // ─── Public auth — no filter needed ───────────────────────────
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login',    'AuthController::login');
    $routes->post('auth/refresh',  'AuthController::refresh');

    // ─── Authenticated routes — require JWT ────────────────────────
    $routes->group('', ['filter' => ['apiCors', 'jwt']], function ($routes) {

        // Auth
        $routes->post('auth/logout', 'AuthController::logout');

        // User / Subscription
        $routes->get('user/me',                    'UserController::me');
        $routes->get('user/subscription',           'UserController::getSubscription');
        $routes->post('user/subscription',         'UserController::setSubscription');
        $routes->get('user/subscription/tiers',     'UserController::getTiers');

        // Storage
        $routes->get('storage',                   'StorageController::index');
        $routes->post('storage/rent',             'StorageController::rent');
        $routes->get('storage/rentals',           'StorageController::rentals');

        // Credentials
        $routes->get('credentials',               'CredentialsController::index');
        $routes->post('credentials/regenerate',   'CredentialsController::regenerate');

        // Activity Logs
        $routes->get('logs',                     'LogsController::index');
    });
});

// Content-only routes for AJAX page loading (without layout wrapper)
// Registered BEFORE spa_fallback so they match before {path} catches them
$routes->get('content/(:segment)', 'Home::content/$1', ['as' => 'page_content']);

// SPA fallback — unmatched routes go to index (hash routing handles pages)
$routes->get('{path}', 'Home::index', ['as' => 'spa_fallback']);
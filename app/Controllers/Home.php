<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Check if user has JWT token (auth check is done in JS for simplicity)
        // The JS router handles the SPA-style navigation after initial load
        return view('auth/index');
    }

    /**
     * Dashboard page (authenticated)
     */
    public function dashboard(): string
    {
        return view('layouts/main', ['page' => 'dashboard', 'pageContent' => view('pages/dashboard')]);
    }

    /**
     * Storage page (authenticated)
     */
    public function storage(): string
    {
        return view('layouts/main', ['page' => 'storage', 'pageContent' => view('pages/storage')]);
    }

    /**
     * Credentials page (authenticated)
     */
    public function credentials(): string
    {
        return view('layouts/main', ['page' => 'credentials', 'pageContent' => view('pages/credentials')]);
    }

    /**
     * Subscription page (authenticated)
     */
    public function subscription(): string
    {
        return view('layouts/main', ['page' => 'subscription', 'pageContent' => view('pages/subscription')]);
    }

    /**
     * Activity logs page (authenticated)
     */
    public function logs(): string
    {
        return view('layouts/main', ['page' => 'logs', 'pageContent' => view('pages/logs')]);
    }

    /**
     * Content-only AJAX route for hash-based SPA navigation
     * Renders just the page content without the layout wrapper
     */
    public function content(string $page = ''): \CodeIgniter\HTTP\ResponseInterface
    {
        $validPages = ['dashboard', 'storage', 'credentials', 'subscription', 'logs'];
        if (!in_array($page, $validPages)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }

        return $this->response->setJSON([
            'html' => view("pages/{$page}"),
            'page' => $page,
        ]);
    }
}

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
        return view('layouts/main', ['page' => 'dashboard']);
    }

    /**
     * Storage page (authenticated)
     */
    public function storage(): string
    {
        return view('layouts/main', ['page' => 'storage']);
    }

    /**
     * Credentials page (authenticated)
     */
    public function credentials(): string
    {
        return view('layouts/main', ['page' => 'credentials']);
    }

    /**
     * Subscription page (authenticated)
     */
    public function subscription(): string
    {
        return view('layouts/main', ['page' => 'subscription']);
    }

    /**
     * Activity logs page (authenticated)
     */
    public function logs(): string
    {
        return view('layouts/main', ['page' => 'logs']);
    }
}

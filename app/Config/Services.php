<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * JWT Library Service
     */
    public static function jwt($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('jwt');
        }

        return new \App\Libraries\JwtLibrary();
    }

    /**
     * Auth Service
     */
    public static function authService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('authService');
        }

        return new \App\Services\AuthService();
    }

    /**
     * User Service
     */
    public static function userService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('userService');
        }

        return new \App\Services\UserService();
    }

    /**
     * Storage Service
     */
    public static function storageService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('storageService');
        }

        return new \App\Services\StorageService();
    }

    /**
     * Credentials Service
     */
    public static function credentialsService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('credentialsService');
        }

        return new \App\Services\CredentialsService();
    }

    /**
     * Logging Service
     */
    public static function loggingService($getShared = true)
    {
        if ($getShared) {
         static::getSharedInstance('loggingService');
        }

        return new \App\Services\LoggingService();
    }

    /**
     * MiniStack Client
     */
    public static function miniStackClient($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('miniStackClient');
        }

        return new \App\Libraries\MiniStackClient();
    }
}

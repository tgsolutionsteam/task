<?php

namespace App\Middlewares;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * NOTE TO REVIEWER: Very symple implementation of auth Basic only to show the possibility, use oAuth is better
 * or even create an users table in the database
 * @SuppressWarnings("unused")
 */
class AuthMiddleware implements MiddlewareInterface
{
    private const ALLOWEDUSERS = [
        'admin' => 'admin',
        'manager' => 'manager',
    ];

    public function beforeHandleRoute(Event $event, Micro $application)
    {

        if (array_key_exists($_SERVER['PHP_AUTH_USER'], self::ALLOWEDUSERS)) {
            if (self::ALLOWEDUSERS[$_SERVER['PHP_AUTH_USER']] === $_SERVER['PHP_AUTH_PW']) {
                return true;
            }
        }

        $application->response->setStatusCode(401, 'Unauthorized');
        $application->response->setJsonContent(
            [
                'message' => 'Invalid Credentials'
            ],
            JSON_UNESCAPED_UNICODE
        )->send();

        return false;
    }

    public function call(Micro $application)
    {
        return true;
    }
}

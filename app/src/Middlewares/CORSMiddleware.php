<?php

namespace App\Middlewares;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * @SuppressWarnings("unused")
 * @codeCoverageIgnore
 */
class CORSMiddleware implements MiddlewareInterface
{

    public function beforeHandleRoute(Event $event, Micro $application)
    {
           $origin = '*';

        if ($application->request->getHeader('ORIGIN')) {
            $origin = $application->request->getHeader('ORIGIN');
        }

        $application->response
            ->setHeader('Access-Control-Allow-Origin', $origin)
            ->setHeader('Access-Control-Allow-Methods', 'PUT, POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Origin, Content-Type');

        if ($application->request->getMethod() === 'OPTIONS') {
            $application->response->setStatusCode(200);
            return false;
        }
    }

    public function call(Micro $application): bool
    {
        return true;
    }
}

<?php

namespace App\Middlewares;

use App\Services\RedisCacheService;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * @SuppressWarnings("unused")
 */
class CacheMiddleware implements MiddlewareInterface
{

    public function beforeHandleRoute(Event $event, Micro $application)
    {

        if (!$application->request->isGet()) {
            return true;
        }

        $redisClient = $application->di->getShared('redisClient');
        $redisService = new RedisCacheService($redisClient);

        $key = $redisService->generateCacheKeyName($application->request->getQuery());

        if (empty($key)) {
            return true;
        }

        $cacheContent =  $redisService->remember($key, function () {
            return false;
        });

        if (!$cacheContent) {
            return true;
        }

        unset($cacheContent['status_code']);
        $application->response->setStatusCode(201);
        $application->response->setJsonContent($cacheContent, JSON_UNESCAPED_UNICODE);

        return false;
    }

    public function call(Micro $application): bool
    {
        return true;
    }
}

<?php

use App\Factories\ErrorFactory;
use App\Services\RedisCacheService;

$app->after(
    function () use ($app): bool {
        $returnedValue = (array) $app->getReturnedValue();

        if (!array_key_exists('status_code', $returnedValue)) {
            $returnedValue['status_code'] = 200;
        }

        if ($returnedValue['status_code'] === 201 && $app->request->getContentType() !== 'text/xml') {
            $redisService = new RedisCacheService($app->di->getShared('redisClient'));

            $keyQuery = $app->request->getQuery();
            // Cache Data When The Request is GET
            if ($app->request->isGet()) {
                $redisService->setCacheData($returnedValue, $keyQuery);
            }

            // Clear Cache to update with new value when API receive a non GET VERB
            if (!$app->request->isGet()) {
                $redisService->clearCacheData($keyQuery);
            }
        }

        if ($app->request->getContentType() !== 'text/xml') {
            $app->response->setStatusCode($returnedValue['status_code']);
            unset($returnedValue['status_code']);
            $app->response->setJsonContent($returnedValue, JSON_UNESCAPED_UNICODE);
        }
        return false;
    }
);

$app->notFound(
    function () use ($app): bool {
        $app->response->setStatusCode(404, 'Not Found');
        $app->response->setJsonContent(
            [
                'message' => 'Not found'
            ]
        );

        return false;
    }
);

$app->error(
    function ($ex) use ($app) {
        if ($ex instanceof Exception) {
            $errorFactory = new ErrorFactory($app->response, $ex);
            $errorFactory->buildErrorMessage();
            return false;
        }

        if ($ex instanceof TypeError) {
            $app->response->setStatusCode(409);
            $app->response->setJsonContent(
                [
                    'message' => 'Invalid Type',
                ]
            );
             return false;
        }

        $app->response->setStatusCode(500);
        $app->response->setJsonContent(
            [
                'message' => $ex->getMessage(),
                'trace' => $ex->getTrace(),
            ]
        );

        return false;
    }
);

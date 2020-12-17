<?php

namespace App\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;
use Redis;

class CacheProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->set(
            'redisClient',
            function () {
                $client = new Redis();
                $host = 'redis';
                if (isset($_SERVER['REDIS_HOST'])) {
                    $host = $_SERVER['REDIS_HOST'];
                }
                $client->connect($host, 6379);
                return $client;
            }
        );
    }
}

<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\CacheMiddleware;
use App\Middlewares\CORSMiddleware;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Micro;

require __DIR__ . '/../../vendor/autoload.php';


$di = new FactoryDefault();

$providers = require __DIR__ . '/providers.php';

foreach ($providers as $provider) {
    $di->register(new $provider());
}

$app = new Micro($di);

require __DIR__ . '/events.php';
require __DIR__ . '/routes.php';

$eventsManager = new EventsManager();
$eventsManager->attach('micro', new CORSMiddleware());
$eventsManager->attach('micro', new AuthMiddleware());
$eventsManager->attach('micro', new CacheMiddleware());
$app->setEventsManager($eventsManager);

return $app;

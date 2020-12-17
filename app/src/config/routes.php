<?php

use App\Controllers\CacheController;
use App\Controllers\CUDControllers\CurrenciesCUDController;
use App\Controllers\CUDControllers\RatesCUDController;
use App\Controllers\IndexController;
use App\Controllers\OpenAPIController;
use App\Controllers\QueryControllers\CurrenciesQueryController;
use App\Controllers\QueryControllers\StudentsCoursesQueryController;
use App\Controllers\QueryControllers\RatesQueryController;
use Phalcon\Mvc\Micro\Collection;

$collection = new Collection();
$collection->setHandler(IndexController::class, true);
$collection->get('/', 'index');
$collection->post('/', 'index');
$app->mount($collection);

$collection = new Collection();
$collection->setHandler(CurrenciesQueryController::class, true);
$collection->setPrefix('/symbols');
$collection->get('/{id}', 'show');
$collection->get('/', 'list');
$app->mount($collection);

$collection = new Collection();
$collection->setHandler(CurrenciesCUDController::class, true);
$collection->setPrefix('/symbols');
$collection->mapVia('/{id}', 'update', [ 'PUT', 'OPTIONS' ]);
$collection->post('/', 'create');
$collection->put('/{symbol_id}', 'update');
$app->mount($collection);

$collection = new Collection();
$collection->setHandler(RatesQueryController::class, true);
$collection->setPrefix('/rates');
$collection->get('/{base}', 'showRates');
$collection->get('/', 'showRates');
$app->mount($collection);

$collection = new Collection();
$collection->setHandler(RatesCUDController::class, true);
$collection->setPrefix('/rates');
$collection->mapVia('/{base_currency_id}/{currency_id}/{date}', 'update', [ 'PUT', 'OPTIONS' ]);
$collection->post('/', 'create');
$collection->put('/{base_currency_id}/{currency_id}/{date}', 'update');
$app->mount($collection);

$collection = new Collection();
$collection->setHandler(CacheController::class, true);
$collection->setPrefix('/cache');
$collection->delete('/', 'clearAll');
$collection->delete('/{tag}', 'clearAll');
$app->mount($collection);

$collection = new Collection();
$collection->setHandler(OpenAPIController::class, true);
$collection->setPrefix('/oas');
$collection->get('/', 'index');
$app->mount($collection);

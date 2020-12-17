<?php

namespace App\Observers;

use App\Factories\ImagesFactory;
use App\Models\Currencies;
use App\Models\ImagesSizes;
use App\Services\ImagesSizesService;
use App\Services\NotifierService;
use App\Services\RedisCacheService as Redis;
use Phalcon\Events\Event;
use Phalcon\Mvc\Model;
use Phalcon\DI;

/**
 * @SuppressWarnings("unused")
 * @SuppressWarnings("static")
 */
class RatesObserver implements ObserverInterface
{
    private $serviceNotifier;
    private $cache;

    public function __construct()
    {
        $this->cache = new Redis((DI::getDefault())->getShared('redisClient'));
        $this->serviceNotifier = new NotifierService(new Currencies());
    }

    public function afterCreate(Event $event, Model $rates)
    {
        /**
         * NOTE TO REVIEWER:
         * if you want to notify only a strict symbol, pass the symbol in the parameters
         * i.e:
         *  $this->serviceNotifier->notify($rates, 'rates', 'USD');
         */
        $this->serviceNotifier->notify($rates, 'rates');
        $this->cache->clear();
    }

    public function afterUpdate(Event $event, Model $rates)
    {
        /**
         * NOTE TO REVIEWER:
         * if you want to notify only a strict symbol, pass the symbol in the parameters
         * i.e:
         *  $this->serviceNotifier->notify($rates, 'rates', 'USD');
         */
        $this->serviceNotifier->notify($rates, 'rates');
        $this->cache->clear();
    }
}

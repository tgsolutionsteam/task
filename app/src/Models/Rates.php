<?php

namespace App\Models;

use App\Observers\RatesObserver;
use Phalcon\Events\Manager as EventsManager;
use App\Models\Interfaces\DAOInterface;

class Rates extends BaseModel implements DAOInterface
{

    public ?int $currency_base_id;
    public ?int $currency_id;
    public ?string $rate_date;
    public ?float $rate;

    public function initialize()
    {
        $eventsManager = new EventsManager();
        $eventsManager->attach('model', new RatesObserver());
        $this->setEventsManager($eventsManager);
    }
}

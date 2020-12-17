<?php

namespace App\Observers;

use Phalcon\Events\Event;
use Phalcon\Mvc\Model;

interface ObserverInterface
{
    public function afterCreate(Event $event, Model $rates);
    public function afterUpdate(Event $event, Model $rates);
}

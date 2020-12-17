<?php

namespace App\Services;

use App\Models\Interfaces\DAOInterface;

interface NotifierServiceInterface
{
    public function notify(DAOInterface $model, string $routingKey, string $strictSymbol = null);
}

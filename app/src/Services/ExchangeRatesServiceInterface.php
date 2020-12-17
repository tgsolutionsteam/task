<?php

namespace App\Services;

interface ExchangeRatesServiceInterface
{
    public function getRates(array $params);
    public function saveRates(object $rates): void;
}

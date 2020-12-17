<?php

namespace App\Services;

use App\HttpClient\Guzzle;
use App\HttpClient\RequestInterface;
use App\Models\Currencies;
use App\Models\Interfaces\DAOInterface;
use App\Models\Rates;
use Exception;

class ExchangeRatesService implements ExchangeRatesServiceInterface
{

    private $baseSymbolId;
    private $symbolId;
    private $rateDate;
    private $client;

    public function __construct(Guzzle $client = null)
    {
        $this->client = $client ?? new Guzzle();
    }

    public function getRates(array $params)
    {
        $data = $this->requestData($params);
        if (isset($data->error)) {
                throw new Exception($data->error);
        }
        $this->saveRates((object)$data);
    }

    public function saveRates(object $rates): void
    {
        $this->baseSymbolId = $this->saveNewCurrency($rates->base);
        if (isset($rates->date)) {
            $this->rateDate = $rates->date;
            $this->createSimpleRates($rates->rates);
            return;
        }
        $this->createDatesRates($rates->rates);
    }

    private function createSimpleRates(array $rates)
    {
        foreach ($rates as $symbol => $rate) {
            $this->symbolId = $this->saveNewCurrency($symbol);
            $this->handleNewRate($rate);
        }
    }

    private function createDatesRates(array $rates)
    {
        foreach ($rates as $date => $dateRate) {
            $this->rateDate = $date;
            foreach ($dateRate as $symbol => $rate) {
                $this->symbolId = $this->saveNewCurrency($symbol);
                $this->handleNewRate($rate);
            }
        }
    }

    private function saveNewCurrency(string $symbol, ?DAOInterface $currency = null): ?int
    {
        $currency = $currency ?? new Currencies();
        $result = $currency->findFirst('symbol="' . $symbol . '"');
        if (!$result && !empty($symbol)) {
            $currency->symbol = $symbol;
            if ($currency->save()) {
                return $currency->id;
            }
        }

        return $result->id;
    }

    private function handleNewRate(float $valueRate, ?DAOInterface $rate = null): ?int
    {
        $rate = $rate ?? new Rates();
        $result = $rate->findFirst(
            'currency_base_id=' . $this->baseSymbolId . ' AND ' .
            'currency_id=' . $this->symbolId  . ' AND ' .
            'rate_date="' . $this->rateDate . '"'
        );
        if (!$result) {
            return $this->createNewRate($valueRate, new Rates());
        }

        $result->rate = $valueRate;
        $result->save();

        return $result->currency_base_id;
    }

    private function createNewRate(float $valueRate, ?DAOInterface $rate): ?int
    {
        $rate->currency_base_id = $this->baseSymbolId;
        $rate->currency_id = $this->symbolId;
        $rate->rate_date = $this->rateDate;
        $rate->rate = $valueRate;
        if ($rate->save()) {
            return $rate->currency_base_id;
        }

        return null;
    }

    protected function requestData(array $params): object
    {

        $endpoint = 'latest';
        if (!empty($params['end_at'])) {
            $endpoint = 'history';
        }

        return $this->client->apiRequest($endpoint, ['query' => $params]);
    }
}

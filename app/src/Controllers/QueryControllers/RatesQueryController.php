<?php

namespace App\Controllers\QueryControllers;

use App\Controllers\BaseControllers\BaseQueryController;
use App\Controllers\Interfaces\ControllerInterface;
use App\Models\ViewCurrenciesRates;
use App\Repositories\Repositories\CurrenciesRatesRepository;
use App\Services\ExchangeRatesService;

class RatesQueryController extends BaseQueryController implements ControllerInterface
{

    public $serviceExchangeRates;
    public $rates = [];

    /**
     * {@inheritdoc}
     */
    public function onConstruct()
    {
        $this->repository = new CurrenciesRatesRepository(new ViewCurrenciesRates());
        $this->serviceExchangeRates = new ExchangeRatesService();
    }

    public function showRates(?string $base = null)
    {
        $base = $base ?? 'EUR';
        $this->arrWhere['eq'][] = [ 'base_symbol' => $base ];

        $this->rates['base'] = $base;

        $this->buildWhereConditions();

        $data = $this->repository->find($this->arrWhere);

        if ($data['resultsFiltered'] === 0) {
            $data = $this->getExternalData($base);
        }

        $this->rates['rates'] = [];
        foreach ($data['data'] as $value) {
            $this->rates['rates'][$value->rate_date][$value->symbol] = $value->rate;
        }

        return $this->rates;
    }

    private function buildWhereConditions(): void
    {
        $startAt = $this->getStartAt();
        if (!$startAt) {
            $this->arrWhere['gte'][] = [ 'rate_date' => date('Y-m-d') ];
            return;
        }
        $this->arrWhere['gte'][] = [ 'rate_date' => $startAt ];

        $endAt = $this->getEndAt();
        if (!$endAt) {
            $this->arrWhere['lte'][] = [ 'rate_date' => $startAt ];
            return;
        }

        $this->arrWhere['lte'][] = [ 'rate_date' => $endAt ];
    }

    public function getStartAt(): ?string
    {
        $startAt = $this->request->get('start_at', 'string') ?? null;
        if ($startAt) {
            $this->rates['start_at'] = $startAt;
            return $startAt;
        }

        $data = ($this->repository->find($this->arrWhere, ['limit => 1']));
        if ($data['resultsFiltered'] === 0) {
            $this->rates['start_at'] = $startAt;
            return date('Y-m-d');
        }
        return $data['data'][0]->rate_date;
    }

    public function getEndAt()
    {
        $endAt = $this->request->get('end_at', 'string') ?? null;
        if ($endAt) {
            $this->rates['end_at'] = $endAt;
            return $endAt;
        }
    }

    public function getExternalData(string $base): array
    {
        $params = [];
        $params['base'] = $base;

        if ($this->request->get('start_at', 'string')) {
            $params['start_at'] = $this->getStartAt();
            $params['end_at'] = $this->getEndAt() ?? $this->getStartAt();
        }

        $this->serviceExchangeRates->getRates($params);

        $this->arrWhere = [];
        $this->arrWhere['eq'][] = [ 'base_symbol' => $base ];
        $this->buildWhereConditions();
        return $this->repository->find($this->arrWhere);
    }
}

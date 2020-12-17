<?php

namespace App\Controllers\CUDControllers;

use App\Controllers\BaseControllers\BaseCUDController;
use App\Controllers\Interfaces\ControllerInterface;
use App\DTO\DTOs\DataTransformerObjectConverter;
use App\DTO\DTOs\RatesDTO;
use App\Models\Rates;
use App\Repositories\Repositories\CurrenciesRatesRepository;

class RatesCUDController extends BaseCUDController implements ControllerInterface
{

    /**
     * {@inheritdoc}
     */
    public function onConstruct()
    {
        $this->repository = new CurrenciesRatesRepository(new Rates());
        $this->dto = (new DataTransformerObjectConverter(new RatesDTO()))->apply($this->request);
    }
}

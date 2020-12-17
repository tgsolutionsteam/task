<?php

namespace App\Controllers\CUDControllers;

use App\Controllers\BaseControllers\BaseCUDController;
use App\Controllers\Interfaces\ControllerInterface;
use App\DTO\DTOs\CurrenciesDTO;
use App\DTO\DTOs\DataTransformerObjectConverter;
use App\Models\Currencies;
use App\Repositories\Repositories\CurrenciesRepository;

class CurrenciesCUDController extends BaseCUDController implements ControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function onConstruct()
    {
        $this->repository = new CurrenciesRepository(new Currencies());
        $this->dto = (new DataTransformerObjectConverter(new CurrenciesDTO()))->apply($this->request);
    }
}

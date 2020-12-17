<?php

namespace Tests\Unit;

use App\DTO\DTOs\CurrenciesDTO;
use Codeception\Test\Unit;
use stdClass;

class CurrenciesDTOTest  extends Unit
{
    public function testShouldPopulateDTOWhenUseItsMethods()
    {
        $dto = new CurrenciesDTO();
        $dto->setId(1);
        $dto->setSymbol('EUR');
        $this->assertEquals(1, $dto->getId());
        $this->assertEquals('EUR', $dto->getSymbol());

        $dto->setId(2);
        $dto->setSymbol('GBP');
        $this->assertEquals(2, $dto->getId());
        $this->assertEquals('GBP', $dto->getSymbol());


        $model = new stdClass();
        $model->id = 2;
        $model->symbol = 'EUR';

        $dto = $dto->transform($model);
        $this->assertEquals(2, $dto->getId());
        $this->assertEquals('EUR', $dto->getSymbol());
    }
}

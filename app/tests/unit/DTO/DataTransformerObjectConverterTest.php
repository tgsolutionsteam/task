<?php

namespace Tests\Unit;


use App\DTO\DTOs\CurrenciesDTO;
use App\DTO\DTOs\DataTransformerObjectConverter;
use App\DTO\DTOs\StudentsCoursesDTO;
use App\DTO\DTOs\RatesDTO;
use Codeception\Test\Unit;
use Phalcon\Http\Request;
use Exception;
use Mockery as m;

class DataTransformerObjectConverterTest extends Unit
{
    public $transformer;
    public $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = m::mock(Request::class);
    }

    /**
     * @dataProvider providerDTO
     */
    public function testShouldApllyDTOWhenReceiveNewRequestToDTO($dtoName, $arrJson, $arrQuery, $arrQuery2)
    {

        $dtoTransformer = new DataTransformerObjectConverter(new $dtoName);

        $this->request->shouldReceive('isGet')
            ->andReturn(false)
            ->once();

        $this->request->shouldReceive('getJsonRawBody')
            ->andReturn((object) $arrJson)
            ->once();

        $this->request->shouldReceive('getQuery')
            ->andReturn($arrQuery)
            ->once();

        $dto = $dtoTransformer->apply($this->request);
        $this->assertInstanceOf($dtoName, $dto);

        if ($dtoName == CurrenciesDTO::class) {
            $this->assertEquals(1, $dto->getId());
            $this->assertEquals('EUR', $dto->getSymbol());
            $this->request->shouldReceive('getQuery')
                ->andReturn($arrQuery2)
                ->once();

            try {
                $dtoTransformer->apply($this->request);
            } catch (Exception $ex) {
                $this->assertInstanceOf(Exception::class, $ex);
            }
        }

        if ($dtoName == RatesDTO::class) {
            $this->assertEquals(1, $dto->getCurrencyBaseId());
        }

        $this->request->shouldReceive('getJsonRawBody')
            ->andReturn((object) ['xpto' => 1, 'nameNonExistent' => 'John'])
            ->once();

        try {
            $dtoTransformer->apply($this->request);
        } catch (Exception $ex) {
            $this->assertInstanceOf(Exception::class, $ex);
        }

    }

    /**
     * @return array
     */
    public function providerDTO()
    {

        return
            [
                [
                    CurrenciesDTO::class,
                    ['id' => 1, 'symbol' => 'EUR'],
                    ['_url' => '/symbols/', 'name' => 'EUR'],
                    ['_url' => '/symbols/', 'sort' => 'symbol'],
                ],
                [
                    RatesDTO::class,
                    ['currencyBaseId' => 1, 'currencyId' => 1, 'rateDate' => '2020-12-17', 'rate' => 1.9090],
                    ['_url' => '/rates/', 'currencyBaseId' => 1],
                    ['_url' => '/rates/', 'sort' => 'John'],
                ]
            ];
    }

}

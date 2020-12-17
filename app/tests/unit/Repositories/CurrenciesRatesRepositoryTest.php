<?php
namespace Tests\Unit;


use App\DTO\DTOs\RatesDTO;
use App\Models\Interfaces\DAOInterface;
use App\Repositories\Repositories\CurrenciesRatesRepository;
use Exception;
use Codeception\Test\Unit;
use Mockery as m;
use Phalcon\Mvc\Model\Row;

class CurrenciesRatesRepositoryTest extends Unit
{
    public $model;
    public $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = m::mock(DAOInterface::class);
        $this->repository = new CurrenciesRatesRepository($this->model);
    }

    public function testShouldNotCreateDataWhenPostWithMissingParametersMethod()
    {

        $data = new RatesDTO();
        $data->setCurrencyBaseId(1);
        $data->setCurrencyId(null);
        $data->setRateDate(date('Y-m-d H:i:s'));
        $data->setRate(0);

        $this->model->currency_base_id = 1;
        $this->model->currency_id = null;
        $this->model->rate_date = null;
        $this->model->rate = null;

        $this->model->shouldReceive('create')
            ->andReturn(false)
            ->once();

        $this->model->shouldReceive('getMessages')
            ->andReturn(['Error']);

        $this->model->shouldReceive('getPrimaryKeyValues')->once()->andReturnSelf();
        try {
            $this->repository->create($data);
        } catch (Exception $ex) {
            $this->assertInstanceOf(Exception::class, $ex);
        }

    }

    public function testShouldNotUpdateDataWhenPostWithMissingParametersMethod()
    {
        $data = new RatesDTO();
        $data->setCurrencyBaseId(1);
        $data->setCurrencyId(null);
        $data->setRate(null);
        $data->setRateDate(date('Y-m-d H:i:s'));

        $mModel = m::mock(DAOInterface::class);

        $this->model->shouldReceive('findFirst')
            ->andReturn($mModel)
            ->once();

        $mModel->shouldReceive('update')
            ->andReturn(false)
            ->once();

        $this->model->shouldReceive('getPrimaryKeyValues')->once()->andReturnSelf();
        try {
            $this->repository->update($data);
        } catch (Exception $ex) {
            $this->assertInstanceOf(Exception::class, $ex);
        }

    }
    public function testShouldCreateDataWhenPostMethod()
    {
        $data = new RatesDTO();
        $data->setCurrencyBaseId(1);
        $data->setCurrencyId(2);
        $data->setRate(0);
        $data->setRateDate(date('Y-m-d H:i:s'));

        $this->model->currency_base_id = 1;
        $this->model->currency_id = 2;
        $this->model->rate = null;
        $this->model->rate_date = null;

        $this->model->shouldReceive('create')
            ->andReturn(true)
            ->once();

        $this->model->shouldReceive('getPrimaryKeyValues')->once()->andReturnSelf();
        $result = $this->repository->create($data);

        $this->assertObjectHasAttribute('currencyBaseId', $result);
        $this->assertObjectHasAttribute('currencyId', $result);
        $this->assertObjectHasAttribute('rateDate', $result);
        $this->assertObjectHasAttribute('rate', $result);


        $this->assertEquals($data->getCurrencyBaseId(), $result->getCurrencyBaseId());
        $this->assertEquals($data->getCurrencyId(), $result->getCurrencyId());
        $this->assertEquals($data->getRateDate(), $result->getRateDate());
        $this->assertEquals($data->getRate(), $result->getRate());


    }

    public function testShouldUpdateDataWhenUpdateMethod()
    {
        $data = new RatesDTO();
        $data->setCurrencyBaseId(1);
        $data->setCurrencyId(2);
        $data->setRate(0);
        $data->setRateDate(date('Y-m-d H:i:s'));

        $this->model->currency_base_id = 1;
        $this->model->currency_id = 2;
        $this->model->rate = null;
        $this->model->rate_date = null;

        $this->model->shouldReceive('findFirst')
            ->andReturn($this->model)
            ->once();

        $this->model->shouldReceive('update')
            ->andReturn(true)
            ->once();

        $this->model->shouldReceive('getPrimaryKeyValues')->once()->andReturnSelf();
        $result = $this->repository->update($data);

        $this->assertObjectHasAttribute('currencyBaseId', $result);
        $this->assertObjectHasAttribute('currencyId', $result);
        $this->assertObjectHasAttribute('rateDate', $result);
        $this->assertObjectHasAttribute('rate', $result);

        $this->assertEquals($data->getCurrencyBaseId(), $result->getCurrencyBaseId());
        $this->assertEquals($data->getCurrencyId(), $result->getCurrencyId());
        $this->assertEquals($data->getRateDate(), $result->getRateDate());
        $this->assertEquals($data->getRate(), $result->getRate());

    }

    /**
     * @dataProvider providerFind
     */
    public function testShouldReturnDataWhenUsingFindMethod($key, $status_code, $data)
    {

        $this->model->shouldReceive('getModelsMetaData')
            ->andReturnSelf();

        $this->model->shouldReceive('getAttributes')
            ->andReturn(['currency_base_id','currency_id']);

        $this->model->shouldReceive('find')
        ->andReturn([ $key => $status_code ])
        ->once();

        $this->model->shouldReceive('count')
                    ->andReturn([ $key => $status_code ]);

        $this->model->shouldReceive('setLimit')
        ->with($data["params"]["options"]["limit"])
        ->andReturn($data["params"]["options"]["limit"])
        ->once();

        $this->model->shouldReceive('setOffset')
        ->with($data["params"]["options"]["offset"])
        ->andReturn($data["params"]["options"]["offset"])
        ->once();

        $this->model->shouldReceive('getColumns')->once()->andReturnSelf();
        $this->repository->setLimit(10);
        $this->repository->setOffset(2);

        if ($status_code == '201') {
            $result = $this->repository->find($data["query"], $data["params"]);
            $this->assertArrayHasKey($key, $result);
            $this->assertEquals($status_code, $result['status_code']);
        }

        if ($status_code == '409') {
            $this->model->shouldReceive('find')
                ->andReturn(false);

            try {
                $this->repository->find($data["query"], $data["params"]);
            } catch (Exception $ex) {
                $this->assertInstanceOf(Exception::class, $ex);
            }
        }
    }

    public function testShouldReturnAnDataWhenUsingFindFisrtMethod()
    {

        $this->model->shouldReceive('getPrimaryKey')->andReturn('currency_base_id');
        $this->model->shouldReceive('getColumns')->once()->andReturnSelf();

        $mModel = m::mock(Row::class);

        $this->model->shouldReceive('findFirst')
            ->andReturn($mModel)
            ->once();

        $result = $this->repository->findFirst(1);
        $this->assertInstanceOf(Row::class, $result);
    }

    public function testShouldReturnAThrowWhenUsingFindFisrtMethod()
    {

        $this->model->shouldReceive('getPrimaryKey')->andReturn('currency_base_id');
        $this->model->shouldReceive('getColumns')->once()->andReturnSelf();

        $this->model->shouldReceive('findFirst')
            ->andReturn(false)
            ->once();

        try {
            $this->repository->findFirst(1);
        } catch (Exception $ex) {
            $this->assertInstanceOf(Exception::class, $ex);
        }

    }

    /**
     * @return array
     */
    public function providerFind()
    {

        return
        [
            [
                "status_code",
                "201",
                [
                    "query" => ["eq" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "",
                        "fields" => "currency_base_id,currency_id",
                        "options" => ["limit" => 10, "offset" => 0]
                    ]
                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => [
                        "eq" => [
                            [
                                "currency_base_id" => "1"
                            ],
                            [
                                "currency_id" => "1"
                            ]
                        ]
                    ],
                    "params" =>[
                        "sort" => "",
                        "options" => ["limit" => 10, "offset" => 0]
                    ]
                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["not" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_id asc",
                        "options" => ["limit" => 10, "offset" => 0]
                    ]
                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["lte" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_id asc",
                        "options" => ["limit" => 10, "offset" => 0]
                    ]
                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["gte" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_currency_id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]
                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["gt" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_id asc, currency_id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["lt" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_id asc, currency_id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["like" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_id asc, currency_id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["in" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_id asc, currency_id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["is" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_id asc, currency_id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "sort" => "currency_base_id asc, currency_id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ],
            [
                "status_code",
                "409",
                [
                    "query" => ["lt" => [[ "currency_base_id" => "1" ]]],
                    "params" =>[
                        "fields" => 'non-existent',
                        "sort" => "currency_base_id asc, currency_id desc",
                        "source" => true,
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ]
        ];
    }
}

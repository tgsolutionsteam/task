<?php
namespace Tests\Unit;

use App\DTO\DTOs\CurrenciesDTO;
use App\Models\Interfaces\DAOInterface;
use App\Repositories\Repositories\CurrenciesRepository;
use Exception;
use Codeception\Test\Unit;
use Mockery as m;
use Phalcon\Mvc\Model\Row;

class CurrenciesRepositoryTest extends Unit
{
    public $model;
    public $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = m::mock(DAOInterface::class);
        $this->repository = new CurrenciesRepository($this->model);
    }

    public function testShouldNotCreateDataWhenPostWithMissingParametersMethod()
    {
        $data = new CurrenciesDTO();
        $data->setId(1);
        $data->setSymbol(null);

        $this->model->id = 1;
        $this->model->symbol = null;

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
        $data = new CurrenciesDTO();
        $data->setId(1);
        $data->setSymbol(null);

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
        $data = new CurrenciesDTO();
        $data->setId(1);
        $data->setSymbol('EUR');

        $this->model->id = 1;
        $this->model->name = 'EUR';


        $this->model->shouldReceive('create')
            ->andReturn(true)
            ->once();

        $this->model->shouldReceive('getPrimaryKeyValues')->once()->andReturnSelf();
        $result = $this->repository->create($data);

        $this->assertObjectHasAttribute('id', $result);
        $this->assertObjectHasAttribute('symbol', $result);

        $this->assertEquals($data->getId(), $result->getId());
        $this->assertEquals($data->getSymbol(), $result->getSymbol());

    }

    public function testShouldUpdateDataWhenUpdateMethod()
    {
        $data = new CurrenciesDTO();
        $data->setId(1);
        $data->setSymbol('EUR');

        $this->model->id = 1;
        $this->model->name = 'EUR';
        $this->model->created_at = null;
        $this->model->modified_at = null;

        $this->model->shouldReceive('findFirst')
            ->andReturn($this->model)
            ->once();

        $this->model->shouldReceive('update')
            ->andReturn(true)
            ->once();

        $this->model->shouldReceive('getPrimaryKeyValues')->once()->andReturnSelf();
        $result = $this->repository->update($data);

        $this->assertObjectHasAttribute('id', $result);
        $this->assertObjectHasAttribute('symbol', $result);

        $this->assertEquals($data->getId(), $result->getId());
        $this->assertEquals($data->getSymbol(), $result->getSymbol());

    }

    /**
     * @dataProvider providerFind
     */
    public function testShouldReturnDataWhenUsingFindMethod($key, $status_code, $data)
    {

        $this->model->shouldReceive('getModelsMetaData')
            ->andReturnSelf();

        $this->model->shouldReceive('getAttributes')
            ->andReturn(['id','name']);

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

        $result = $this->repository->find($data["query"], $data["params"]);
        $this->assertArrayHasKey($key, $result);
        $this->assertEquals($status_code, $result['status_code']);
    }

    public function testShouldReturnAnDataWhenUsingFindFisrtMethod()
    {

        $this->model->shouldReceive('getPrimaryKey')->andReturn('id');
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

        $this->model->shouldReceive('getPrimaryKey')->andReturn('id');
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
                    "query" => ["eq" => [[ "symbol" => "EUR" ]]],
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
                    "query" => [
                        "eq" => [
                            [
                                "symbol" => "EUR"
                            ],
                            [
                                "id" => "1"
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
                    "query" => ["not" => [[ "symbol" => "EUR" ]]],
                    "params" =>[
                        "sort" => "symbol asc",
                        "options" => ["limit" => 10, "offset" => 0]
                    ]
                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["lte" => [[ "symbol" => "EUR" ]]],
                    "params" =>[
                        "sort" => "symbol asc",
                        "options" => ["limit" => 10, "offset" => 0]
                    ]
                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["gte" => [[ "symbol" => "EUR" ]]],
                    "params" =>[
                        "sort" => "symbol desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]
                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["gt" => [[ "symbol" => "EUR" ]]],
                    "params" =>[
                        "sort" => "symbol asc, id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ],
            [
                "status_code",
                "201",
                [
                    "query" => ["lt" => [[ "symbol" => "EUR" ]]],
                    "params" =>[
                        "sort" => "symbol asc, id desc",
                        "options" => ["limit" => 10, "offset" => 1]
                    ]

                ]
            ]
        ];
    }
}

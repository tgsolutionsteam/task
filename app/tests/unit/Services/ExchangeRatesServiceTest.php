<?php

namespace Tests\Unit;

use App\HttpClient\Guzzle;
use App\Services\ExchangeRatesService;
use Codeception\Test\Unit;
use Mockery as m;


class ExchangeRatesServiceTest extends Unit
{
    public $adapter;
    public $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = m::mock(Guzzle::class);
        $this->service = new ExchangeRatesService($this->client);
    }

    public function testShouldGetSimpleRatesWhenCalled()
    {
        $data = (object) [
            "rates" => [
                "CAD" => 1.5546,
            ],
            "base" => "EUR",
            "date" => "2020-12-17"
        ];
        $this->client->shouldReceive('apiRequest')->andReturn($data)->once();
        $this->service->getRates(['base' => 'EUR']);

    }

    public function testShouldGetComplexRatesWhenCalled()
    {
        $data = (object) [
            "rates" => [
                "2020-12-17" => [
                    "DKK" => 1.5546,
                ]
            ],
            "base" => "JPY",
        ];
        $this->client->shouldReceive('apiRequest')->andReturn($data)->once();
        $this->service->getRates([
            'base' => 'EUR',
            'start_at' => '2020-12-15',
            'end_at' => '2020-12-16',
        ]);

    }

}

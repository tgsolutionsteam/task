<?php
namespace Tests\Unit;

use App\Models\Rates;
use Codeception\Test\Unit;

class RatesTest extends Unit
{

    public $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Rates();
    }

    public function testShouldReturnPropertiesOfModelWhenUsingTheirMethod()
    {
        $this->assertEquals('currency_base_id,currency_id,rate_date,rate', $this->model->getColumns());
        $this->assertEquals('currency_base_id', $this->model->getPrimaryKey());
    }
}

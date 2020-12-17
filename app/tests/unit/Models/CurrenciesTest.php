<?php
namespace Tests\Unit;

use App\Models\Currencies;
use Codeception\Test\Unit;

class CurrenciesTest extends Unit
{

    public $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Currencies();
    }

    public function testShouldReturnPropertiesOfModelWhenUsingTheirMethod()
    {
        $this->assertEquals('id,symbol', $this->model->getColumns());
        $this->assertEquals('id', $this->model->getPrimaryKey());
    }
}

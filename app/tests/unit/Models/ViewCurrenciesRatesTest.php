<?php
namespace Tests\Unit;

use App\Models\ViewCurrenciesRates;
use Codeception\Test\Unit;

class ViewCurrenciesRatesTest extends Unit
{

    public $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new ViewCurrenciesRates();
    }

    public function testShouldReturnPropertiesOfModelWhenUsingTheirMethod()
    {
        $this->assertEquals('base_symbol,symbol,rate_date,rate', $this->model->getColumns());
    }
}

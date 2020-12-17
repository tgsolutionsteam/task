<?php
namespace Tests\Unit;

use App\Services\UtilsService;
use Codeception\Test\Unit;

class UtilsServiceTest extends Unit
{
    public function testShouldReturnAnArrayOfSortWhenPassAString()
    {
        $util = new UtilsService();
        $arrSort = $util->formatSort('name,asc;id,desc');

        $this->assertEquals('name asc', $arrSort[0]);
        $this->assertEquals('id desc', $arrSort[1]);
    }
}

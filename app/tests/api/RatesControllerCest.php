<?php

use Codeception\Util\HttpCode;

class RatesControllerCest
{

    public function testShouldShowSymbolsWhenGet(ApiTester $I)
    {
        $I->amHttpAuthenticated('admin', 'admin');
        $I->sendGET('/rates');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

}

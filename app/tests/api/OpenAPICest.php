<?php

use Codeception\Util\HttpCode;

class OpenAPICest
{

    public function testIndex(ApiTester $I)
    {
        $I->amHttpAuthenticated('admin', 'admin');
        $I->sendGET('/oas');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }
}

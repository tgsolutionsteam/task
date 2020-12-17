<?php

use Codeception\Util\HttpCode;

class IndexControllerCest
{

    public function testIndex(ApiTester $I)
    {
        $data = "<methodCall>
   <methodName>getRates</methodName>
   <params>
      <param>
         <value><string>EUR</string></value>
      </param>
   </params>
</methodCall>";

        $I->amHttpAuthenticated('admin', 'admin');
        $I->haveHttpHeader('Content-Type', 'text/xml');
        $I->sendPOST('/', $data);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContains('<?xml version="1.0" encoding="utf-8"?>');
    }
}

<?php

use Codeception\Util\HttpCode;

class CurrenciesControllerCest
{

    public function testShouldPopulateCurrenciesWhenPost(ApiTester $I)
    {
        $faker = Faker\Factory::create();
        $data = [
            'symbol' => $faker->password(3,3),
        ];

        $I->amHttpAuthenticated('admin', 'admin');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/symbols', $data);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('new_id');
    }

    public function testShouldShowSymbolsWhenGet(ApiTester $I)
    {
        $I->amHttpAuthenticated('admin', 'admin');
        $I->sendGET('/symbols');
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('resultsFiltered');

    }

    public function testShouldPopulateCurrenciesWhenPut(ApiTester $I)
    {
        $data = [
            'symbol' => 'YES',
        ];

        $I->amHttpAuthenticated('admin', 'admin');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/symbols/1', $data);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'message' => 'Updated successfully'
            ]
        );
    }
}

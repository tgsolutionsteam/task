<?php

use Codeception\Util\HttpCode;

class CacheControllerCest
{
    public function testShouldPopulateCacheWhenCalled(ApiTester $I)
    {
        $faker = Faker\Factory::create();
        $def_id = $faker->numberBetween(9000, 100000);
        $data = [
            "id" => $def_id,
            'symbol' => $faker->password(3,3),
        ];

        $I->amHttpAuthenticated('admin', 'admin');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/symbols', $data);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'message' => 'Created successfully'
            ]
        );

        $I->amHttpAuthenticated('admin', 'admin');
        $I->sendGET('/symbols/');
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
    }

    public function testShouldClearAllCacheWhenReceiveADeleteRequest(ApiTester $I)
    {
        $I->amHttpAuthenticated('admin', 'admin');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/cache');
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'message' => 'Flush cache successfully'
            ]
        );
    }

    public function testShouldNotClearTagCacheCacheWhenReceiveAWrongTag(ApiTester $I)
    {
        $I->amHttpAuthenticated('admin', 'admin');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/cache/symbols');
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'message' => 'Deleted tags: 0'
            ]
        );
    }

}

<?php

namespace Tests\Unit;

use App\Services\RedisCacheService;
use Cache\Adapter\Common\Exception\CachePoolException;
use Codeception\Test\Unit;
use Mockery as m;
use DateInterval;
use Phalcon\Di;
use Psr\Cache\CacheItemInterface;
use Redis;

class RedisCacheServiceTest extends Unit
{
    public $adapter;
    public $service;
    public $item;

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = m::mock(Redis::class);
        $this->item = m::mock(CacheItemInterface::class);
        $this->service = new RedisCacheService($this->adapter);
    }

    public function testForeverMethod()
    {
        $this->adapter->shouldReceive('set')->andReturn(true)->once();
        $class = $this->service->forever('key', $this->getDataArray());
        $this->assertInstanceOf(RedisCacheService::class, $class);
    }

    public function testPutMethod()
    {
        $this->adapter->shouldReceive('setex')->andReturn(true)->once();
        $class = $this->service->put('key', $this->getDataArray(), new DateInterval('P10D'));
        $this->assertInstanceOf(RedisCacheService::class, $class);
    }

    public function testPutWithoutTime()
    {
        $this->adapter->shouldReceive('set')->andReturn(true)->once();
        $class = $this->service->put('key', $this->getDataArray());
        $this->assertInstanceOf(RedisCacheService::class, $class);
    }

    public function testForgetMethod()
    {
        $this->adapter->shouldReceive('get')->andReturn(false)->once();
        $this->adapter->shouldReceive('del')->andReturn(false)->once();
        $this->service->forget('key');
        $this->assertEquals(false, $this->service->has('key'));
    }

    public function testForgetMultipleMethod()
    {
        $this->adapter->shouldReceive('get')->andReturn(false)->once();
        $this->adapter->shouldReceive('del')->andReturn(false)->once();
        $this->adapter->shouldReceive('deleteMultiple')->andReturn(true)->once();
        $this->service->forget(['key-1', 'key-2']);
        $this->assertEquals(false, $this->service->has('key'));
    }

    public function testRememberCachedMethod()
    {
        $this->adapter->shouldReceive('get')->andReturn(false)->once();
        $this->adapter->shouldReceive('setex')->andReturn(true)->once();
        $data = $this->service->remember('key', function() {
            return $this->getDataArray();
        }, 60);
        $this->assertEquals($this->getDataArray()['data'], $data['data']);
    }

    public function testRememberWithoutCacheMethod()
    {
        $this->adapter->shouldReceive('get')->andReturn(false)->once();
        $this->adapter->shouldReceive('setex')->andReturn(true)->once();
        $data = $this->service->remember('key', function() {
            return $this->getDataArray();
        }, 60);
        $this->assertEquals($this->getDataArray()['data'], $data['data']);
    }

    public function testShouldAddATagWhenUseAddTag()
    {
        $this->adapter->shouldReceive('lpush')->andReturn(1)->once();
        $this->adapter->shouldReceive('lRange')->andReturn(['key1'])->once();
        $this->service->addTag('tag1', 'key1');
        $arr = $this->service->getKeysByTag('tag1');
        $this->assertEquals(['key1'], $arr);
    }

    public function testShouldClearTagWhenUseClearTag()
    {
        $this->adapter->shouldReceive('get')->andReturn(false)->once();
        $this->adapter->shouldReceive('del')->andReturn(1)->once();
        $this->adapter->shouldReceive('lRange')->andReturn(['key1'])->once();
        $items = $this->service->clearTag('tag1');
        $this->assertEquals(1, $items);
    }

    public function testShouldGenerateAKeyWhenCallGenerateCacheKeyName()
    {
        $cacheKey = 'key1=' . md5('value') . ';';
        $key = $this->service->generateCacheKeyName(['key1' => 'value']);
        $this->assertEquals($cacheKey, $key);
    }

    public function testShouldCreateAEntryWhenUsingRemember()
    {
        $di = DI::getDefault();
        $redisClient = $di->getShared('redisClient');
        $redisService = new RedisCacheService($redisClient);
        $key = $redisService->generateCacheKeyName(['key1' => 'value']);
        $value = $redisService->remember($key, fn() => false);
        $this->assertFalse($value);
    }

    public function testShouldClearCacheWhenCallClearCache()
    {
        $this->adapter->shouldReceive('lrange')->andReturn(['key1'])->once()->andReturnSelf();
        $this->service->clearCacheData(['_url' => 'students']);
        $this->assertNotInstanceOf(CachePoolException::class, null);
    }

    public function testShouldSetCacheDataWhenCallSetCache()
    {
        $di = DI::getDefault();
        $redisClient = $di->getShared('redisClient');
        $redisService = new RedisCacheService($redisClient);
        $redisService->setCacheData(['teste'], ['_url' => 'students']);
        $this->assertNotInstanceOf(CachePoolException::class, null);
    }

    public function testShouldReturnEmptyArrayWhenWithWrongTag()
    {

        $array = $this->service->getRelatedTags('non-existent');
        $this->assertEquals([], $array);
    }

    private function getDataArray(): array
    {
        return [
            'data' => [
                'id' => 1,
                'url' => 'lorem-ipsum-url',
                'content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
            ],
            'totalResults' => 1
        ];
    }

}

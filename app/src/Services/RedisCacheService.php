<?php

namespace App\Services;

use Cache\Adapter\Redis\RedisCachePool;

class RedisCacheService extends RedisCachePool implements CacheServiceInterface
{
    private const TAGS = [
        'students' => ['courses'],
        'courses' => ['students'],
    ];

    /**
     * {@inheritdoc}
     */
    public function getRelatedTags(string $tag): array
    {
        if (array_key_exists($tag, self::TAGS)) {
            return self::TAGS[$tag];
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(array $tagArray): string
    {
        $tag = isset($tagArray['_url']) ? str_replace('/', '', $tagArray['_url']) : null;
        $tag .= $tagArray['website_id'] ?? '';

        return $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeysByTag(string $tag): array
    {
        return $this->getList($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function addTag(string $tag, string $key): void
    {
        $this->appendListItem($tag, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function clearTag(string $tag): int
    {
        $deletedItems = 0;
        foreach ($this->getList($tag) as $value) {
            if ($this->forget($value)) {
                $deletedItems++;
            }
        }

        return $deletedItems;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheData(array $returnedValue, array $keyQuery): void
    {
        $key = $this->generateCacheKeyName($keyQuery);
        $tagQuery = $this->getTag($keyQuery);

        if (!empty($key)) {
            $this->put($key, $returnedValue);
            $this->addTag($tagQuery, $key);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearCacheData(array $keyQuery): void
    {
        $tagQuery = $this->getTag($keyQuery);
        $this->clearTag($tagQuery);
        $relatedTags = $this->getRelatedTags($tagQuery);
        foreach ($relatedTags as $tag) {
            $this->clearTag($tag);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $key, $value, $time = null)
    {
        if ($time === null) {
            return $this->forever($key, $value);
        }
        $this->set($key, $value, $time);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function forever(string $key, $value)
    {
        $this->set($key, $value);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function forget($keys): bool
    {
        if (is_array($keys)) {
            return $this->deleteMultiple($keys);
        }
        return $this->deleteItem($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function remember(string $key, callable $callback, $time = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        $value = $callback();
        $this->put($key, $value, $time);
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCacheKeyName(array $keys): string
    {
        $cacheKey = '';
        foreach ($keys as $key => $value) {
            $cacheKey .= $key . '=' . md5($value) . ';';
        }
        return $cacheKey;
    }
}

<?php

namespace App\Services;

interface CacheServiceInterface
{
    /**
     * Persist the result of a request in Cache and sets the tags for them
     * @param array $returnedValue
     * @param array $keyQuery
     */
    public function setCacheData(array $returnedValue, array $keyQuery): void;

    /**
     * Remove a content from cache using a query string
     * @param array $keyQuery
     */
    public function clearCacheData(array $keyQuery): void;

    /**
     * Creates a tag related with the $key passed, with this is possible to delete multiples kyes by tag
     * @param string $tag
     * @param string $key
     */
    public function addTag(string $tag, string $key): void;

    /**
     * Extract a tag from an array of Tags extracted from query string
     * @param array $tagArray
     * @return string
     */
    public function getTag(array $tagArray): string;

    /**
     * Remove the keys associated to the tag passed.
     * @param string $tag
     * @return int
     */
    public function clearTag(string $tag): int;

    /**
     * Gets all tags related with the tag passed.
     * @param string $tag
     * @return array
     */
    public function getRelatedTags(string $tag): array;

    /**
     * Retrieve all keys related to the key passed
     * @param string $tag
     * @return array
     */
    public function getKeysByTag(string $tag): array;

    /**
     * Create an entry in cache associated to a key
     * @param string $key
     * @param $value
     * @param null $time
     * @return mixed
     */
    public function put(string $key, $value, $time = null);

    /**
     * Creates an entry without expiration time
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function forever(string $key, $value);

    /**
     * Removes a key from cache
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool;

    /**
     * Provides the ability to easily do read-through caching.
     *
     * When called if the $key is not set in $config, the $callable function
     * will be invoked. The results will then be stored into the cache config
     * at key.
     * @param string $key
     * @param callable $callback
     * @param null $time
     * @return mixed
     */
    public function remember(string $key, callable $callback, $time = null);

    /**
     * Generate a MD5 key value with the array of keys from query string
     * @param array $keys
     * @return string
     */
    public function generateCacheKeyName(array $keys): string;
}

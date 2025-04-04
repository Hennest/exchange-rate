<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Closure;

interface CacheInterface
{
    /**
     * Checks if the specified cache key exists in the cache.
     */
    public function exist(string $cacheKey): bool;

    /**
     * Retrieves a cached value for the specified cache key.
     */
    public function get(string $cacheKey): mixed;

    /**
     * Stores a value in the cache with the specified cache key.
     *
     * @param int|null $ttl (Optional) The cache lifetime in seconds for the stored value.
     */
    public function put(string $cacheKey, mixed $value, null|int $ttl = null): bool;

    /**
     * Removes a cached value for the specified cache key.
     */
    public function forget(string $cacheKey): bool;

    /**
     * Retrieves a cached value for the specified cache key or stores a new value if it doesn't exist.
     *
     * @param Closure(): array<string, string> $callback A closure that generates the value to be stored if the cache key doesn't exist.
     *
     * @param int|null $ttl (Optional) The cache lifetime in seconds for the stored or retrieved value.
     */
    public function remember(string $cacheKey, Closure $callback, null|int $ttl = null): mixed;
}

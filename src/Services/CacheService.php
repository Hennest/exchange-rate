<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Closure;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Psr\SimpleCache\InvalidArgumentException;

final class CacheService implements CacheInterface
{
    public function __construct(
        protected CacheContract $cache,
        protected string $prefix,
        protected int $ttl
    ) {
    }

    /**
     * @param string[] $cacheKey
     */
    private function cacheKey(array $cacheKey): string
    {
        return $this->prefix . "." . implode('.', $cacheKey);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function exist(array $cacheKey): bool
    {
        return $this->cache->has(
            $this->cacheKey($cacheKey)
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(array $cacheKey): mixed
    {
        return $this->cache->get(
            $this->cacheKey($cacheKey)
        );
    }

    public function put(array $cacheKey, mixed $value, ?int $ttl = null): bool
    {
        return $this->cache->put(
            key: $this->cacheKey($cacheKey),
            value: $value,
            ttl: $ttl ?? $this->ttl
        );
    }

    public function forget(array $cacheKey): bool
    {
        return $this->cache->forget(
            $this->cacheKey($cacheKey)
        );
    }

    /**
     * @param Closure(): array<string, string> $callback
     */
    public function remember(array $cacheKey, Closure $callback, ?int $ttl = null): mixed
    {
        return $this->cache->remember(
            key: $this->cacheKey($cacheKey),
            ttl: $ttl ?? $this->ttl,
            callback: $callback
        );
    }
}

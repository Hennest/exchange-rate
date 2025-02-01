<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Closure;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Illuminate\Contracts\Cache\Repository as CacheContract;

final readonly class CacheService implements CacheInterface
{
    public function __construct(
        private CacheContract $cache,
        private string $prefix,
        private int $ttl
    ) {
    }

    public function exist(string $cacheKey): bool
    {
        return $this->cache->has(
            $this->cacheKey($cacheKey)
        );
    }

    public function get(string $cacheKey): mixed
    {
        return $this->cache->get(
            $this->cacheKey($cacheKey)
        );
    }

    public function put(string $cacheKey, mixed $value, null|int $ttl = null): bool
    {
        return $this->cache->put(
            key: $this->cacheKey($cacheKey),
            value: $value,
            ttl: $ttl ?? $this->ttl
        );
    }

    public function forget(string $cacheKey): bool
    {
        return $this->cache->forget(
            $this->cacheKey($cacheKey)
        );
    }

    /**
     * @param Closure(): array<string, string> $callback
     */
    public function remember(string $cacheKey, Closure $callback, null|int $ttl = null): mixed
    {
        return $this->cache->remember(
            key: $this->cacheKey($cacheKey),
            ttl: $ttl ?? $this->ttl,
            callback: $callback
        );
    }

    private function cacheKey(string $cacheKey): string
    {
        return mb_strtolower($this->prefix . "." . $cacheKey);
    }
}

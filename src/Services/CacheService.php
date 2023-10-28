<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Carbon\Carbon;
use Closure;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Psr\SimpleCache\InvalidArgumentException;

class CacheService implements CacheInterface
{
    protected const CACHE_LIFE_TIME_IN_HOURS = 6;

    protected const PREFIX = 'exchange_rate';

    public function __construct(
        protected CacheRepository $cache,
    ) {
    }

    /**
     * @param string[] $cacheKey
     */
    protected function cacheKey(array $cacheKey): string
    {
        return self::PREFIX . "." . implode('.', $cacheKey);
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

    public function put(array $cacheKey, mixed $value, ?int $cacheLifetimeInHours = null): bool
    {
        return $this->cache->put(
            key: $this->cacheKey($cacheKey),
            value: $value,
            ttl: Carbon::now()->addHours(
                value: $cacheLifetimeInHours ?? config(
                    key: 'exchange-rate.cache.ttl',
                    default: self::CACHE_LIFE_TIME_IN_HOURS
                )
            )
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
    public function remember(array $cacheKey, Closure $callback, ?int $cacheLifetimeInHours = null): mixed
    {
        return $this->cache->remember(
            key: $this->cacheKey($cacheKey),
            ttl: Carbon::now()->addSeconds(
                value: $cacheLifetimeInHours ?? config(
                    key: 'exchange-rate.cache.ttl',
                    default: self::CACHE_LIFE_TIME_IN_HOURS
                )
            ),
            callback: $callback
        );
    }
}

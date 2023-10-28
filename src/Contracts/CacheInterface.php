<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Closure;

interface CacheInterface
{
    /**
     * @param string[] $cacheKey
     */
    public function exist(array $cacheKey): bool;

    /**
     * @param string[] $cacheKey
     */
    public function get(array $cacheKey): mixed;

    /**
     * @param string[] $cacheKey
     */
    public function put(array $cacheKey, mixed $value, ?int $cacheLifetimeInHours = null): bool;

    /**
     * @param string[] $cacheKey
     */
    public function forget(array $cacheKey): bool;

    /**
     * @param string[] $cacheKey
     */
    public function remember(array $cacheKey, Closure $callback, ?int $cacheLifetimeInHours = null): mixed;
}

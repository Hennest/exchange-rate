<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

interface CacheInterface
{
    public function exist(array $cacheKey): bool;

    public function get(array $cacheKey): mixed;

    public function put(array $cacheKey, mixed $value, int $cacheLifetimeInHours): bool;

    public function forget(array $cacheKey): bool;

    public function remember(array $cacheKey, callable $callback, int $cacheLifetimeInHours): mixed;
}

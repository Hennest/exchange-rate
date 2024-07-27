<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\CacheInterface;

it('returns false when cache does not exist', function (): void {
    $exchangeRateCache = app(CacheInterface::class);

    $exchangeRateCache->forget('usd');

    expect($exchangeRateCache->exist('usd'))->toBeFalse();
})->group('exchangeCache');

it('returns true when cache exists', function (): void {
    $exchangeRateCache = app(CacheInterface::class);

    $exchangeRateCache->put('usd', ['usd' => 1.23]);

    expect($exchangeRateCache->exist('usd'))->toBeTrue();
})->group('exchangeCache');

it('returns expected value when cache exists', function (): void {
    $exchangeRateCache = app(CacheInterface::class);

    $exchangeRate = ['usd' => 1.23];

    $exchangeRateCache->put('usd', $exchangeRate);

    expect($exchangeRateCache->get('usd'))->toBe($exchangeRate);
})->group('exchangeCache');

it('returns null when cache does not exist', function (): void {
    $exchangeRateCache = app(CacheInterface::class);

    $exchangeRateCache->forget('usd');

    expect($exchangeRateCache->get('usd'))->toBeNull();
})->group('exchangeCache');

it('can clear cache', function (): void {
    $exchangeRateCache = app(CacheInterface::class);

    $exchangeRateCache->put('usd', ['usd' => 1.23]);

    expect($exchangeRateCache->forget('usd'))->toBeTrue();
})->group('exchangeCache');

it('can store cache', function (): void {
    $exchangeRateCache = app(CacheInterface::class);

    $exchangeRate = ['usd' => 1.23];

    $exchangeRateCache->put('usd', $exchangeRate);

    expect($exchangeRateCache->get('usd'))->toBe($exchangeRate);
})->group('exchangeCache');

it('can get or store cache', function (): void {
    $exchangeRateCache = app(CacheInterface::class);

    $exchangeRate = ['usd' => 1.23];

    $exchangeRateCache->remember('usd', fn () => $exchangeRate);

    expect($exchangeRateCache->get('usd'))->toBe($exchangeRate);
})->group('exchangeCache');

it('can generate cache key', function (): void {
    $cache = app(CacheInterface::class);
    $reflectionClass = new ReflectionClass($cache);
    $cacheKey = $reflectionClass->getMethod('cacheKey');
    $prefix = $reflectionClass->getProperty('prefix');

    $actualCacheKey = $cacheKey->invoke($cache, 'usd');
    $expectedCacheKey = "{$prefix->getValue($cache)}.usd";

    expect($actualCacheKey)->toBe($expectedCacheKey);
})->group('exchangeCache');

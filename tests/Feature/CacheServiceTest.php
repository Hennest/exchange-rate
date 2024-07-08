<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\CacheInterface;

it('returns false when cache does not exist', function (): void {
    $exchangeRateCache = app(CacheInterface::class);
    $currencies = [
        'usd',
        'eur',
    ];

    $exchangeRateCache->forget($currencies);

    expect($exchangeRateCache->exist($currencies))->toBeFalse();
})->group('exchangeCache');

it('returns true when cache exists', function (): void {
    $exchangeRateCache = app(CacheInterface::class);
    $currencies = [
        'usd',
        'eur',
    ];
    $exchangeRate = ['usd' => 1.23];

    $exchangeRateCache->put($currencies, $exchangeRate);

    expect($exchangeRateCache->exist($currencies))->toBeTrue();
})->group('exchangeCache');

it('returns expected value when cache exists', function (): void {
    $exchangeRateCache = app(CacheInterface::class);
    $currencies = [
        'usd',
        'eur',
    ];
    $exchangeRate = ['usd' => 1.23];

    $exchangeRateCache->put($currencies, $exchangeRate);

    expect($exchangeRateCache->get($currencies))->toBe($exchangeRate);
})->group('exchangeCache');

it('returns null when cache does not exist', function (): void {
    $exchangeRateCache = app(CacheInterface::class);
    $currencies = [
        'usd',
        'eur',
    ];

    $exchangeRateCache->forget($currencies);

    expect($exchangeRateCache->get($currencies))->toBeNull();
})->group('exchangeCache');

it('can clear cache', function (): void {
    $exchangeRateCache = app(CacheInterface::class);
    $currencies = [
        'usd',
        'eur',
    ];
    $exchangeRate = ['usd' => 1.23];

    $exchangeRateCache->put($currencies, $exchangeRate);

    expect($exchangeRateCache->forget($currencies))->toBeTrue();
})->group('exchangeCache');

it('can store cache', function (): void {
    $exchangeRateCache = app(CacheInterface::class);
    $currencies = [
        'usd',
        'eur',
    ];
    $exchangeRate = ['usd' => 1.23];

    $exchangeRateCache->put($currencies, $exchangeRate);

    expect($exchangeRateCache->get($currencies))->toBe($exchangeRate);
})->group('exchangeCache');

it('can get or store cache', function (): void {
    $exchangeRateCache = app(CacheInterface::class);
    $currencies = [
        'usd',
        'eur',
    ];
    $exchangeRate = ['usd' => 1.23];

    $exchangeRateCache->remember($currencies, fn () => $exchangeRate);

    expect($exchangeRateCache->get($currencies))->toBe($exchangeRate);
})->group('exchangeCache');

it('can generate cache key', function (): void {
    $cache = app(CacheInterface::class);
    $reflectionClass = new ReflectionClass($cache);
    $cacheKey = $reflectionClass->getMethod('cacheKey');
    $prefix = $reflectionClass->getProperty('prefix');

    $currencies = [
        'usd',
        'eur',
    ];

    $actualCacheKey = $cacheKey->invoke($cache, $currencies);
    $expectedCacheKey = $prefix->getValue($cache) . '.' . implode('.', $currencies);

    expect($actualCacheKey)->toBe($expectedCacheKey);
})->group('exchangeCache');

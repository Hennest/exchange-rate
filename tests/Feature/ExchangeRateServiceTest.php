<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Hennest\ExchangeRate\Tests\Feature\Data\ApiData;

it('returns cached exchange rates if available', function (): void {
    app()->bind(ApiInterface::class, fn () => new ApiData);

    $exchangeRateService = app(ExchangeRateInterface::class);

    $result = $exchangeRateService->rates(['usd', 'eur', 'gbp']);

    expect($result)->toBe([
        'usd' => 1.0,
        'eur' => 0.82,
        'gbp' => 0.72,
    ]);
})->group('exchangeService');

it('returns exchange rate for a given currency', function (): void {
    app()->bind(ApiInterface::class, fn () => new ApiData);

    $exchangeRateService = app(ExchangeRateInterface::class);

    $result = $exchangeRateService->getRate(
        currency: 'usd'
    );

    expect($result)->toBe(1.0);
})->group('exchangeService');

it('can convert exchange rate for currencies', function (): void {
    app()->bind(ApiInterface::class, fn () => new ApiData);

    $exchangeRateService = app(ExchangeRateInterface::class);

    $result = $exchangeRateService->convert(
        amount: 2,
        fromCurrency: 'usd',
        toCurrency: 'eur',
    );

    expect($result)->toBe(1.64);
})->group('exchangeService');

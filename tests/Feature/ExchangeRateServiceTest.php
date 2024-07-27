<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;

it('returns exchange rates', function (): void {
    $exchangeRateService = app(ExchangeRateInterface::class);

    $result = $exchangeRateService->rates(['usd', 'eur', 'gbp']);

    expect($result)->toBe([
        'USD' => 1.0,
        'EUR' => 0.82,
        'GBP' => 0.72,
    ]);
})->group('exchangeService');

it('returns all exchange rates when currencies parameter is not provided', function (): void {
    $exchangeRateService = app(ExchangeRateInterface::class);

    $result = $exchangeRateService->rates();

    expect($result)->toBe([
        'USD' => 1.0,
        'EUR' => 0.82,
        'GBP' => 0.72,
    ]);
})->group('exchangeService');

it('returns exchange rate for a given currency', function (): void {
    $exchangeRateService = app(ExchangeRateInterface::class);

    $result = $exchangeRateService->getRate(
        currency: 'usd'
    );

    expect($result)->toBe(1.0);
})->group('exchangeService');

it('can convert exchange rate for currencies', function (): void {
    $exchangeRateService = app(ExchangeRateInterface::class);

    $result = $exchangeRateService->convert(
        amount: 2,
        fromCurrency: 'usd',
        toCurrency: 'eur',
    );

    expect($result)->toBe(1.64);
})->group('exchangeService');

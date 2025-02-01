<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Hennest\ExchangeRate\Contracts\ResponseAssemblerInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;

it('returns exchange rates', function (): void {
    $exchangeRateService = app(ExchangeRateInterface::class);

    $result = $exchangeRateService->rates(['usd', 'eur', 'gbp']);

    expect($result)->toBe([
        'USD' => 1.0,
        'EUR' => 0.82,
        'GBP' => 0.72,
    ]);
})->group('exchangeService');

it('returns exchange rates from cache if available', function (): void {
    $apiData = new class implements ApiInterface {
        public string $baseCurrency = 'USD';

        public function fetch(): ResponseInterface
        {
            throw new Exception('This method should not be called');
        }
    };
    app()->bind(ApiInterface::class, $apiData::class);

    $response = app(ResponseAssemblerInterface::class)->create(
        baseCurrency: 'USD',
        date: today(),
        rates: [
            'usd' => 1.0,
            'eur' => 0.82,
            'gbp' => 0.72,
        ]
    );

    app(CacheInterface::class)->put('USD', $response);

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

    expect($result->value)->toBe('1.64');
})->group('exchangeService');

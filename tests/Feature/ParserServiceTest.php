<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrency;
use Hennest\ExchangeRate\Tests\Feature\Data\ApiData;

it('returns correct format', function (): void {
    $this->app->bind(ApiInterface::class, fn () => new ApiData);

    $exchangeRateApi = app(ApiInterface::class);
    $exchangeRateParser = app(ParserInterface::class);

    $parsedResult = [
        'usd' => 1.0,
        'eur' => 0.82,
        'gbp' => 0.72,
    ];

    $result = $exchangeRateParser->parse(
        exchangeRate: $exchangeRateApi->fetch(),
        toCurrencies: ['usd', 'eur', 'gbp']
    );

    expect($result)->toBe($parsedResult);
})->group('exchangeParser');

it('ignores unsupported currency', function (): void {
    $this->app->bind(ApiInterface::class, fn () => new ApiData);

    $exchangeRateApi = app(ApiInterface::class);
    $exchangeRateParser = app(ParserInterface::class);

    $parsedResult = [
        'usd' => 1.0,
        'eur' => 0.82,
        'gbp' => 0.72,
    ];

    $result = $exchangeRateParser->parse(
        exchangeRate: $exchangeRateApi->fetch(),
        toCurrencies: ['usd', 'eur', 'gbp']
    );

    expect($result)->toBe($parsedResult);
})->group('exchangeParser');

it('throws exception when currency is unavailable', function (): void {
    $exchangeRateParser = app(ParserInterface::class);

    $exchangeRate = [
        'usd' => 1.0,
        'eur' => 0.8,
    ];
    $toCurrencies = ['GBP'];

    expect(fn () => $exchangeRateParser->parse($exchangeRate, $toCurrencies))->toThrow(
        exception: InvalidCurrency::class,
        exceptionMessage: "Exchange rate data for currency 'GBP' is not available."
    );
})->group('exchangeParser');

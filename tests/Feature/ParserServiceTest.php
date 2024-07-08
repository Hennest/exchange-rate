<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrencyException;

it('returns correct format', function (): void {
    $exchangeRateApi = app(ApiInterface::class);
    $exchangeRateParser = app(ParserInterface::class);

    $result = $exchangeRateParser->parse(
        response: $exchangeRateApi->fetch(),
        toCurrencies: ['usd', 'eur', 'gbp']
    );

    expect($result)->toBe([
        'USD' => 1.0,
        'EUR' => 0.82,
        'GBP' => 0.72,
    ]);
})->group('exchangeParser');

it('throws exception when currency is unavailable', function (): void {
    $exchangeRateApi = app(ApiInterface::class);
    $exchangeRateParser = app(ParserInterface::class);

    $toCurrencies = ['AUD', 'BRL', 'JPY'];

    expect(fn () => $exchangeRateParser->parse($exchangeRateApi->fetch(), $toCurrencies))->toThrow(
        exception: InvalidCurrencyException::class,
        exceptionMessage: sprintf(
            "Exchange rate data for currencies '%s' is not available.",
            implode(', ', $toCurrencies)
        )
    );
})->group('exchangeParser');

it('throws exception when toCurrency is empty', function (): void {
    $exchangeRateApi = app(ApiInterface::class);
    $exchangeRateParser = app(ParserInterface::class);

    expect(fn () => $exchangeRateParser->parse($exchangeRateApi->fetch(), []))->toThrow(
        exception: InvalidArgumentException::class,
        exceptionMessage: 'The toCurrencies array cannot be empty.'
    );
})->group('exchangeParser');

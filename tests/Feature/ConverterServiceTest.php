<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\ConverterInterface;
use Hennest\ExchangeRate\Services\ConverterService;

it('can convert exchange rates', function (): void {
    $exchangeRateConverter = app(ConverterInterface::class);

    $result = $exchangeRateConverter->convert(
        amount: 2,
        fromRate: 3.239,
        toRate: 6.382,
    );

    expect($result->value)->toBe('3.9407224452');
})->group('exchangeConverter');

it('can convert exchange rates with config scale', function (): void {
    app()->when(ConverterService::class)->needs('$scale')->give(5);
    $exchangeRateConverter = app(ConverterInterface::class);

    $result = $exchangeRateConverter->convert(
        amount: 2,
        fromRate: 3.239,
        toRate: 6.382,
    );

    expect($result->value)->toBe('3.94072');
})->group('exchangeConverter');

it('can convert exchange rates with custom scale', function (): void {
    $exchangeRateConverter = app(ConverterInterface::class);

    $result = $exchangeRateConverter->convert(
        amount: 2,
        fromRate: 3.239,
        toRate: 6.382,
        scale: 3
    );

    expect($result->value)->toBe('3.941');
})->group('exchangeConverter');

it('can convert exchange rates with very small amount', function (): void {
    $exchangeRateConverter = app(ConverterInterface::class);

    $result = $exchangeRateConverter->convert(
        amount: 0.005,
        fromRate: 0.00239,
        toRate: 0.0000022,
        scale: 10
    );

    expect($result->value)->toBe('0.0000046025');
})->group('exchangeConverter');

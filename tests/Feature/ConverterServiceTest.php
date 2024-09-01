<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\ConverterInterface;

it('can convert exchange rates', function (): void {
    $exchangeRateConverter = app(ConverterInterface::class);

    $result = $exchangeRateConverter->convert(
        amount: 2,
        fromRate: 3,
        toRate: 6
    );

    expect($result)->toBe(4.0);
})->group('exchangeConverter');

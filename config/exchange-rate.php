<?php

declare(strict_types=1);

return [
    'base_currency' => 'USD',

    /**
     * Arbitrary Precision Calculator.
     *
     * 'scale' - length of the mantissa
     */
    'math' => [
        'scale' => env('EXCHANGE_RATE_SCALE', 2),
    ],

    /**
     * Storage of the state of the balance of wallets.
     */
    'cache' => [
        'driver' => 'array',
        'ttl' => 6 * 3600,
    ],

    /**
     * Services that can be overloaded.
     */
    'services' => [
        'api' => \Hennest\ExchangeRate\Drivers\CurrencyApiService::class,
        'cache' => \Hennest\ExchangeRate\Services\CacheService::class,
        'parser' => \Hennest\ExchangeRate\Services\ParserService::class,
        'exchange_rate' => \Hennest\ExchangeRate\Services\ExchangeRateService::class,
    ]
];

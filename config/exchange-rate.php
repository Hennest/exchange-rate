<?php

declare(strict_types=1);

return [
    'base_currency' => env('EXCHANGE_RATE_BASE_CURRENCY', 'USD'),

    /**
     * Arbitrary Precision Calculator.
     *
     * 'scale' - length of the mantissa
     */
    'math' => [
        'scale' => env('EXCHANGE_RATE_SCALE', 10),
    ],

    /**
     * Storage of the state of the exchange rates.
     *
     * Supported drivers: same as laravel cache
     *
     */
    'cache' => [
        'prefix' => env('EXCHANGE_RATE_CACHE_PREFIX', 'exchange_rate'),
        'driver' => env('EXCHANGE_RATE_CACHE_DRIVER', env('CACHE_DRIVER')),
        'ttl' => env('EXCHANGE_RATE_CACHE_TTL', 6 * 3600),
    ],

    /**
     * Builder classes, needed to create DTO.
     */
    'assemblers' => [
        'response' => \Hennest\ExchangeRate\Assembler\ResponseAssembler::class,
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

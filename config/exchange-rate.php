<?php

declare(strict_types=1);

return [

    /**
     * Base currency for the exchange rate.
     */
    'base_currency' => env('EXCHANGE_RATE_BASE_CURRENCY', 'USD'),

    /**
     * API key for the exchange rate service.
     */
    'api_key' => env('EXCHANGE_RATE_API_KEY', ''),

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
        'driver' => env('EXCHANGE_RATE_CACHE_DRIVER', env('CACHE_STORE', 'file')),
        'ttl' => env('EXCHANGE_RATE_CACHE_TTL', 6 * 3600),
    ],

    /**
     * Builder classes, needed to create DTO.
     */
    'assemblers' => [
        'response' => Hennest\ExchangeRate\Assembler\ResponseAssembler::class,
    ],

    /**
     * Services that can be overloaded.
     */
    'services' => [
        'api' => Hennest\ExchangeRate\Drivers\CurrencyApiService::class,
        'cache' => Hennest\ExchangeRate\Services\CacheService::class,
        'parser' => Hennest\ExchangeRate\Services\ParserService::class,
        'exchange_rate' => Hennest\ExchangeRate\Services\ExchangeRateService::class,
    ],

    /**
     * Default driver.
     */
    'default_driver' => env('EXCHANGE_RATE_DRIVER', 'currency-api'),

    /**
     * List of available drivers.
     */
    'drivers' => [
        'currency-api' => [
            'api' => Hennest\ExchangeRate\Drivers\CurrencyApiService::class,
        ],

        'currency-beacon' => [
            'api' => Hennest\ExchangeRate\Drivers\CurrencyBeaconApiService::class,
        ],
    ],

];

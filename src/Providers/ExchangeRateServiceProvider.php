<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Providers;

use Hennest\ExchangeRate\Assembler\ResponseAssembler;
use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Contracts\ResponseAssemblerInterface;
use Hennest\ExchangeRate\Drivers\CurrencyApiService;
use Hennest\ExchangeRate\Services\CacheService;
use Hennest\ExchangeRate\Services\ExchangeRateService;
use Hennest\ExchangeRate\Services\ParserService;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\ServiceProvider;

final class ExchangeRateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/exchange-rate.php' => config_path('exchange-rate.php'),
        ], 'exchange-rate-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            path: __DIR__ . '/../../config/exchange-rate.php',
            key: 'exchange-rate'
        );

        /**
         * @var array{
         *     base_currency: string|null,
         *     api_key: string|null,
         *     math: array{
         *         scale: int|null
         *     },
         *     services?: array{
         *         api: class-string|null,
         *         cache: class-string|null,
         *         parser: class-string|null,
         *         exchange_rate: class-string|null,
         *     },
         *     assemblers?: array{
         *         response: class-string|null,
         *     },
         *     cache?: array{
         *         driver: string|null,
         *         prefix: string|null,
         *         ttl: int|null,
         *     },
         *     default_driver?: string|null,
         *     drivers: array<string, array{
         *          api: class-string|null,
         *          cache: class-string|null,
         *          parser: class-string|null,
         *          exchange_rate: class-string|null,
         *     }>,
         * } $configure
         */
        $configure = config('exchange-rate', []);

        $this->app->singleton(
            abstract: ResponseAssemblerInterface::class,
            concrete: $configure['assemblers']['response'] ?? ResponseAssembler::class
        );

        $this->app->singleton(
            abstract: ParserInterface::class,
            concrete: $driver['parser'] ?? ParserService::class
        );

        $this->assignDriver($configure);
    }

    private function assignDriver(array $configure): void
    {
        /** @var array{
         *     api: class-string|null,
         *     cache: class-string|null,
         *     parser: class-string|null,
         *     exchange_rate: class-string|null,
         * } $driver
         */
        $driver = $configure['drivers'][$configure['default_driver']] ?? $configure['services'];

        $this->setupCacheService(
            cache: $driver['cache'] ?? CacheService::class,
            store: $configure['cache']['driver'] ?? 'array',
            prefix: $configure['cache']['prefix'] ?? 'exchange_rate',
            ttl: $configure['cache']['ttl'] ?? 6 * 3600,
        );

        $this->setupApiService(
            api: $driver['api'] ?? CurrencyApiService::class,
            baseCurrency: $configure['base_currency'] ?? 'USD',
            apiKey: $configure['api_key'] ?? 'YOUR_API',
        );

        $this->setupExchangeRateService(
            exchangeRate: $driver['exchange_rate'] ?? ExchangeRateService::class,
            baseCurrency: $configure['base_currency'] ?? 'USD',
            mathScale: $configure['math']['scale'] ?? 10,
        );
    }

    private function setupCacheService(string $cache, string $store, string $prefix, int $ttl): void
    {
        $this->app->when($cache)
            ->needs(CacheContract::class)
            ->give(function () use ($store) {
                /** @var Factory $factory */
                $factory = $this->app->get(
                    id: CacheFactory::class
                );

                return clone $factory->store(
                    name: $store
                );
            });

        $this->app->when($cache)
            ->needs('$prefix')
            ->give($prefix);

        $this->app->when($cache)
            ->needs('$ttl')
            ->give($ttl);

        $this->app->singleton(
            abstract: CacheInterface::class,
            concrete: $cache
        );
    }

    private function setupApiService(string $api, string $baseCurrency, string $apiKey): void
    {
        $this->app->when($api)
            ->needs('$baseCurrency')
            ->give($baseCurrency);

        $this->app->when($api)
            ->needs('$apiKey')
            ->give($apiKey);

        $this->app->singleton(
            abstract: ApiInterface::class,
            concrete: $api
        );
    }

    private function setupExchangeRateService(string $exchangeRate, string $baseCurrency, int $mathScale): void
    {
        $this->app->when($exchangeRate)
            ->needs('$baseCurrency')
            ->give($baseCurrency);

        $this->app->when($exchangeRate)
            ->needs('$scale')
            ->give($mathScale);

        $this->app->singleton(
            abstract: ExchangeRateInterface::class,
            concrete: $exchangeRate
        );
    }
}

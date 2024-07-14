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

        $this->assignDriver($configure);
    }

    /**
     * @param array{
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
     *     default_driver?: string|null,
     *     drivers: array<string, array{
     *         api: class-string|null,
     *         cache: class-string|null,
     *         parser: class-string|null,
     *         exchange_rate: class-string|null,
     *     }>,
     *     cache?: array{
     *         driver: string|null,
     *         prefix: string|null,
     *         ttl: int|null,
     *     },
     * } $configure
     */
    private function assignDriver(array $configure): void
    {
        /** @var array{
         *     api: class-string|null,
         *     cache: class-string|null,
         *     parser: class-string|null,
         *     exchange_rate: class-string|null,
         * } $driver
         */
        $driver = config('exchange-rate')['drivers'][$configure['default_driver']] ?? $configure['services'];

        $this->app->singleton(
            abstract: ParserInterface::class,
            concrete: $driver['parser'] ?? ParserService::class
        );

        $this->setupCacheService(
            cacheClass: $driver['cache'] ?? CacheService::class,
            store: $configure['cache']['driver'] ?? 'array',
            prefix: $configure['cache']['prefix'] ?? 'exchange_rate',
            ttl: $configure['cache']['ttl'] ?? 6 * 3600,
        );

        $this->setupApiService(
            apiClass: $driver['api'] ?? CurrencyApiService::class,
            baseCurrency: $configure['base_currency'] ?? 'USD',
            apiKey: $configure['api_key'] ?? '',
        );

        $this->setupExchangeRateService(
            exchangeRateClass: $driver['exchange_rate'] ?? ExchangeRateService::class,
            baseCurrency: $configure['base_currency'] ?? 'USD',
            mathScale: $configure['math']['scale'] ?? 10,
        );
    }

    /**
     * @param class-string<CacheInterface> $cacheClass
     */
    private function setupCacheService(string $cacheClass, string $store, string $prefix, int $ttl): void
    {
        $this->app->when($cacheClass)
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

        $this->app->when($cacheClass)
            ->needs('$prefix')
            ->give($prefix);

        $this->app->when($cacheClass)
            ->needs('$ttl')
            ->give($ttl);

        $this->app->singleton(
            abstract: CacheInterface::class,
            concrete: $cacheClass
        );
    }

    /**
     * @param class-string<ApiInterface> $apiClass
     */
    private function setupApiService(string $apiClass, string $baseCurrency, string $apiKey): void
    {
        $this->app->when($apiClass)
            ->needs('$baseCurrency')
            ->give($baseCurrency);

        $this->app->when($apiClass)
            ->needs('$apiKey')
            ->give($apiKey);

        $this->app->bind(
            abstract: ApiInterface::class,
            concrete: $apiClass
        );
    }

    /**
     * @param class-string<ExchangeRateInterface> $exchangeRateClass
     */
    private function setupExchangeRateService(string $exchangeRateClass, string $baseCurrency, int $mathScale): void
    {
        $this->app->when($exchangeRateClass)
            ->needs('$baseCurrency')
            ->give($baseCurrency);

        $this->app->when($exchangeRateClass)
            ->needs('$scale')
            ->give($mathScale);

        $this->app->singleton(
            abstract: ExchangeRateInterface::class,
            concrete: $exchangeRateClass
        );
    }
}

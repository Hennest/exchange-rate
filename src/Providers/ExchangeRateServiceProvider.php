<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Providers;

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Drivers\CurrencyApiService;
use Hennest\ExchangeRate\Services\CacheService;
use Hennest\ExchangeRate\Services\ExchangeRateService;
use Hennest\ExchangeRate\Services\ParserService;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\ServiceProvider;

class ExchangeRateServiceProvider extends ServiceProvider
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
         *     cache?: array{driver: string|null},
         *     services?: array{
         *         api: class-string|null,
         *         cache: class-string|null,
         *         parser: class-string|null,
         *         exchange_rate: class-string|null,
         *     },
         * } $configure
         */
        $configure = config('exchange-rate', []);

        $this->app->when($configure['services']['cache'] ?? CacheService::class)
            ->needs(CacheContract::class)
            ->give(function () use ($configure) {
                /** @var Factory $factory */
                $factory = $this->app->make(
                    abstract: CacheFactory::class
                );

                return clone $factory->store(
                    name: $configure['cache']['driver'] ?? 'array'
                );
            });

        $this->app->when($configure['services']['cache'] ?? CacheService::class)
            ->needs('$prefix')
            ->giveConfig('exchange-rate.cache.prefix');

        $this->app->singleton(
            abstract: CacheInterface::class,
            concrete: $configure['services']['cache'] ?? CacheService::class
        );

        $this->app->when($configure['services']['api'] ?? CurrencyApiService::class)
            ->needs('$baseCurrency')
            ->giveConfig('exchange-rate.base_currency');

        $this->app->singleton(
            abstract: ApiInterface::class,
            concrete: $configure['services']['api'] ?? CurrencyApiService::class
        );

        $this->app->singleton(
            abstract: ParserInterface::class,
            concrete: $configure['services']['parser'] ?? ParserService::class
        );

        $this->app->when($configure['services']['exchange_rate'] ?? ExchangeRateService::class)
            ->needs('$baseCurrency')
            ->giveConfig('exchange-rate.base_currency');

        $this->app->when($configure['services']['exchange_rate'] ?? ExchangeRateService::class)
            ->needs('$scale')
            ->giveConfig('exchange-rate.math.scale');

        $this->app->singleton(
            abstract: ExchangeRateInterface::class,
            concrete: $configure['services']['exchange_rate'] ?? ExchangeRateService::class
        );
    }
}
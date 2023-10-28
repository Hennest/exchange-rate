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
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Support\ServiceProvider;

class ExchangeRateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/exchange-rate.php' => config_path('exchange-rate.php'),
        ], 'exchange-rate-config');
    }

    public function register(): void
    {
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

        $this->app->when($configure['services']['exchange_rate'] ?? ExchangeRateService::class)
            ->needs(CacheInterface::class)
            ->give(function () use ($configure) {
                return $this->app->make(
                    abstract: CacheInterface::class,
                    parameters: [
                        'cache' => clone $this->app
                            ->make(CacheFactory::class)
                            ->store($configure['cache'] ?? 'array'),
                    ]
                );
            });
        $this->app->singleton(
            abstract: CacheInterface::class,
            concrete: $configure['services']['cache'] ?? CacheService::class
        );

        $this->app->singleton(
            abstract: ApiInterface::class,
            concrete: $configure['services']['api'] ?? CurrencyApiService::class
        );

        $this->app->singleton(
            abstract: ParserInterface::class,
            concrete: $configure['services']['parser'] ?? ParserService::class
        );

        $this->app->singleton(ExchangeRateInterface::class, function () {
            return new ExchangeRateService(
                $this->app->make(CacheInterface::class),
                $this->app->make(ApiInterface::class),
                $this->app->make(ParserInterface::class),
            );
        });
    }
}

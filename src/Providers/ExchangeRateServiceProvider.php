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
         *     assemblers?: array{
         *         response: class-string|null,
         *     },
         * } $config
         */
        $config = config('exchange-rate', []);

        $this->app->singleton(
            abstract: ResponseAssemblerInterface::class,
            concrete: $config['assemblers']['response'] ?? ResponseAssembler::class
        );

        $this->setupServices($config);
    }

    /**
     * @param array{
     *     base_currency: string|null,
     *     api_key: string|null,
     *     math: array{
     *         scale: string|null
     *     }|null,
     *     cache: array{
     *         driver: string|null,
     *         prefix: string|null,
     *         ttl: string|null,
     *     }|null,
     * } $config
     */
    private function setupServices(array $config): void
    {
        $services = $this->getServices($config);

        $this->setupParserService(
            parserClass: $services['parser'] ?? ParserService::class
        );

        $this->setupCacheService(
            cacheClass: $services['cache'] ?? CacheService::class,
            store: $config['cache']['driver'] ?? 'array',
            prefix: $config['cache']['prefix'] ?? 'exchange_rate',
            ttl: $config['cache']['ttl'] ?? (string) (6 * 3600),
        );

        $this->setupApiService(
            apiClass: $services['api'] ?? CurrencyApiService::class,
            baseCurrency: $config['base_currency'] ?? 'USD',
            apiKey: $config['api_key'] ?? '',
        );

        $this->setupExchangeRateService(
            exchangeRateClass: $services['exchange_rate'] ?? ExchangeRateService::class,
            baseCurrency: $config['base_currency'] ?? 'USD',
            mathScale: $config['math']['scale'] ?? '10',
        );
    }

    /**
     * @param class-string<ParserInterface> $parserClass
     */
    public function setupParserService(string $parserClass): void
    {
        $this->app->singleton(
            abstract: ParserInterface::class,
            concrete: $parserClass
        );
    }

    /**
     * @param class-string<CacheInterface> $cacheClass
     */
    private function setupCacheService(string $cacheClass, string $store, string $prefix, string $ttl): void
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
    private function setupExchangeRateService(string $exchangeRateClass, string $baseCurrency, string $mathScale): void
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

    /**
     * @param array{
     *     default_driver: string|null,
     *     drivers: array<string, array<string, class-string|null>>|null,
     *     services: array<string, class-string|null>|null
     * } $config
     * @return array{
     *     api: class-string<ApiInterface>|null,
     *     cache: class-string<CacheInterface>|null,
     *     parser: class-string<ParserInterface>|null,
     *     exchange_rate: class-string<ExchangeRateInterface>|null,
     * }
     */
    public function getServices(array $config): array
    {
        return $config['drivers'][$config['default_driver']] ?? $config['services'];
    }
}

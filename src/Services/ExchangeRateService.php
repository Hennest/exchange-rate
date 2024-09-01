<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Hennest\ExchangeRate\Contracts\ConverterInterface;
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Hennest\ExchangeRate\Contracts\ParserInterface;

final readonly class ExchangeRateService implements ExchangeRateInterface
{
    public function __construct(
        private ApiInterface $api,
        private CacheInterface $cache,
        private ConverterInterface $converter,
        private ParserInterface $parser,
        private string $baseCurrency,
    ) {
    }

    public function rates(array|null $currencies = null): array
    {
        if ( ! $rates = $this->cache->get($this->baseCurrency)) {
            $response = $this->api->fetch();

            $this->cache->put(
                cacheKey: $this->baseCurrency,
                value: $response
            );

            $rates = $response;
        }

        return $this->parser->parse(
            response: $rates,
            toCurrencies: $currencies
        );
    }

    public function getRate(string $currency): float
    {
        return (float) $this->rates([$currency])[
            $this->transformCase($currency)
        ];
    }

    public function convert(float|int|string $amount, string $fromCurrency, string $toCurrency, int|null $scale = null): float
    {
        $rates = $this->rates([
            $fromCurrency = $this->transformCase($fromCurrency),
            $toCurrency = $this->transformCase($toCurrency)
        ]);

        return $this->converter->convert(
            amount: $amount,
            fromRate: $rates[$fromCurrency],
            toRate: $rates[$toCurrency],
            scale: $scale
        );
    }

    private function transformCase(string $currency): string
    {
        return mb_strtoupper($currency);
    }
}

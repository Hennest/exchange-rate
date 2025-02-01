<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use BcMath\Number;
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
    ) {
    }

    public function rates(array|null $currencies = null): array
    {
        $baseCurrency = $this->convertCase($this->api->baseCurrency);

        if ( ! $rates = $this->cache->get($baseCurrency)) {
            $response = $this->api->fetch();

            $this->cache->put(
                cacheKey: $baseCurrency,
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
            $this->convertCase($currency)
        ];
    }

    public function convert(Number|int|string $amount, string $fromCurrency, string $toCurrency, int|null $scale = null): Number
    {
        $rates = $this->rates([
            $fromCurrency = $this->convertCase($fromCurrency),
            $toCurrency = $this->convertCase($toCurrency),
        ]);

        return $this->converter->convert(
            amount: (string) $amount,
            fromRate: (string) $rates[$fromCurrency],
            toRate: (string) $rates[$toCurrency],
            scale: $scale
        );
    }

    private function convertCase(string $currency): string
    {
        return match (true) {
            0 === $this->parser->case => mb_strtolower($currency),
            default => mb_strtoupper($currency),
        };
    }
}

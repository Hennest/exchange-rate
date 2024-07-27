<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Hennest\ExchangeRate\Contracts\ParserInterface;

final readonly class ExchangeRateService implements ExchangeRateInterface
{
    public function __construct(
        private CacheInterface $cache,
        private ApiInterface $api,
        private ParserInterface $parser,
        private string $baseCurrency,
        private int $scale,
    ) {
    }

    public function rates(array $currencies): array
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

    public function convert(float|int|string $amount, string $fromCurrency, string $toCurrency, ?int $scale = null): float
    {
        $rates = $this->rates([
            $fromCurrency = $this->transformCase($fromCurrency),
            $toCurrency = $this->transformCase($toCurrency)
        ]);

        $exchangeRate = BigDecimal::of($rates[$toCurrency])
            ->dividedBy(
                that: $rates[$fromCurrency],
                scale: $scale ?? $this->scale,
                roundingMode: RoundingMode::HALF_UP
            );

        return BigDecimal::of($amount)
            ->multipliedBy($exchangeRate)
            ->toScale(
                scale: $scale ?? $this->scale,
                roundingMode: RoundingMode::HALF_UP
            )
            ->toFloat();
    }

    private function transformCase(string $currency): string
    {
        return mb_strtoupper($currency);
    }
}

<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\RoundingMode;
use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrency;
use Illuminate\Http\Client\RequestException;

class ExchangeRateService implements ExchangeRateInterface
{
    public function __construct(
        protected CacheInterface $cache,
        protected ApiInterface $api,
        protected ParserInterface $parser,
        protected string $baseCurrency,
        protected int $scale,
    ) {
    }

    /**
     * @throws InvalidCurrency
     * @throws RequestException
     */
    public function rates(array $currencies): array
    {
        if ($value = $this->cache->get([$this->baseCurrency])) {
            return $this->parser->parse(
                exchangeRates: $value,
                toCurrencies: $currencies
            );
        }

        $exchangeRates = $this->api->fetch();

        $this->cache->put(
            cacheKey: [
                $this->baseCurrency,
            ],
            value: $exchangeRates,
        );

        return $this->parser->parse(
            exchangeRates: $exchangeRates,
            toCurrencies: $currencies
        );
    }

    /**
     * @throws RequestException
     * @throws InvalidCurrency
     */
    public function getRate(string $currency): float
    {
        return (float) $this->rates([$currency])[mb_strtoupper($currency)];
    }

    /**
     * @throws RequestException
     * @throws InvalidCurrency
     * @throws MathException
     */
    public function convert(float|int|string $amount, string $fromCurrency, string $toCurrency, ?int $scale = null): float
    {
        $rates = $this->rates([
            $fromCurrency = mb_strtoupper($fromCurrency),
            $toCurrency = mb_strtoupper($toCurrency)
        ]);

        $exchangeRate = BigDecimal::of($rates[$toCurrency])
            ->dividedBy(
                that: $rates[$fromCurrency],
            );

        return BigDecimal::of($amount)
            ->multipliedBy($exchangeRate)
            ->toScale(
                scale: $scale ?? $this->scale,
                roundingMode: RoundingMode::DOWN
            )->toFloat();
    }
}

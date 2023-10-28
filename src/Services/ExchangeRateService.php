<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\CacheInterface;
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrency;
use Hennest\ExchangeRate\Exceptions\RequestFailed;

class ExchangeRateService implements ExchangeRateInterface
{
    protected const SCALE = 2;

    public function __construct(
        protected CacheInterface  $cache,
        protected ApiInterface    $api,
        protected ParserInterface $parser,
    ) {
    }

    /**
     * @throws InvalidCurrency
     * @throws RequestFailed
     */
    public function rates(array $currencies): array
    {
        if ($value = $this->cache->get($currencies)) {
            return $value;
        }

        $exchangeRates = $this->parser->parse(
            exchangeRate: $this->api->fetch(),
            toCurrencies: $currencies
        );

        $this->cache->put(
            cacheKey: $currencies,
            value: $exchangeRates,
        );

        return $exchangeRates;
    }

    /**
     * @throws RequestFailed
     * @throws InvalidCurrency
     */
    public function getRate(string $currency): float
    {
        return (float) $this->rates([$currency])[$currency];
    }

    /**
     * @throws RequestFailed
     * @throws InvalidCurrency
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function convert(float|int|string $amount, string $fromCurrency, string $toCurrency, ?int $scale = null): float
    {
        $rates = $this->rates([
            $fromCurrency = mb_strtolower($fromCurrency),
            $toCurrency = mb_strtolower($toCurrency)
        ]);

        $exchangeRate = BigDecimal::of($rates[$toCurrency])
            ->dividedBy(
                that: $rates[$fromCurrency],
                scale: $scale ?? config('exchange-rate.math.scale', self::SCALE),
                roundingMode: RoundingMode::DOWN
            );

        return BigDecimal::of($amount)
            ->multipliedBy($exchangeRate)
            ->toScale(
                scale: $scale ?? config('exchange-rate.math.scale', self::SCALE),
                roundingMode: RoundingMode::DOWN
            )->toFloat();
    }
}

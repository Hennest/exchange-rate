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
use Hennest\ExchangeRate\Exceptions\InvalidCurrencyException;
use Illuminate\Http\Client\RequestException;

final class ExchangeRateService implements ExchangeRateInterface
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
     * @throws InvalidCurrencyException
     * @throws RequestException
     */
    public function rates(array $currencies): array
    {
        if ($value = $this->cache->get([$this->baseCurrency])) {
            return $this->parser->parse(
                response: $value,
                toCurrencies: $currencies
            );
        }

        $response = $this->api->fetch();

        $this->cache->put(
            cacheKey: [
                $this->baseCurrency,
            ],
            value: $response,
        );

        return $this->parser->parse(
            response: $response,
            toCurrencies: $currencies
        );
    }

    /**
     * @throws RequestException
     * @throws InvalidCurrencyException
     */
    public function getRate(string $currency): float
    {
        return (float) $this->rates([$currency])[mb_strtoupper($currency)];
    }

    /**
     * @throws RequestException
     * @throws InvalidCurrencyException
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

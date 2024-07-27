<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Brick\Math\Exception\MathException;
use Hennest\ExchangeRate\Exceptions\InvalidCurrencyException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Psr\SimpleCache\InvalidArgumentException;

interface ExchangeRateInterface
{
    /**
     * Get exchange rates for a list of currencies relative to the base currency.
     *
     * @param string[] $currencies An array of currency codes for which exchange rates are requested.
     *
     * @return array<string, float|int> An associative array where currency codes are keys, and exchange rates are values.
     *
     * @throws ConnectionException If the request to the API fails.
     * @throws InvalidArgumentException MUST be thrown if the cache $key string is not a legal value.
     * @throws InvalidCurrencyException If an invalid currency is provided in the list.
     * @throws RequestException If the request to the API fails.
     */
    public function rates(array $currencies): array;

    /**
     * Get the exchange rate for a specific currency relative to the base currency.
     *
     * @throws ConnectionException If the request to the API fails.
     * @throws InvalidArgumentException MUST be thrown if the cache $key string is not a legal value.
     * @throws InvalidCurrencyException If an invalid currency code is provided.
     * @throws RequestException If the request to the API fails.
     */
    public function getRate(string $currency): float;

    /**
     * Convert an amount from one currency to another.
     *
     * @param int|null $scale (Optional) The scale for the result of the conversion. If not provided,
     *                        it uses the default scale or a custom scale from the configuration.
     *
     * @throws ConnectionException If the request to the API fails.
     * @throws InvalidArgumentException MUST be thrown if the cache $key string is not a legal value.
     * @throws InvalidCurrencyException If an invalid currency code is provided.
     * @throws MathException If a mathematical operation fails during conversion.
     * @throws RequestException If the request to the API fails.
     */
    public function convert(float|int|string $amount, string $fromCurrency, string $toCurrency, ?int $scale = null): float;
}

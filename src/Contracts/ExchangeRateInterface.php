<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Brick\Math\Exception\MathException;
use Hennest\ExchangeRate\Exceptions\InvalidCurrency;
use Hennest\ExchangeRate\Exceptions\RequestFailed;

interface ExchangeRateInterface
{
    /**
     * Get exchange rates for a list of currencies relative to the base currency.
     *
     * @param string[] $currencies
     *   An array of currency codes for which exchange rates are requested.
     *
     * @return array
     *   An associative array where currency codes are keys, and exchange rates are values.
     *
     * @throws InvalidCurrency
     *   If an invalid currency is provided in the list.
     *
     * @throws RequestFailed
     *   If the request to the API fails.
     */
    public function rates(array $currencies): array;

    /**
     * Get the exchange rate for a specific currency relative to the base currency.
     *
     * @param string $currency
     *   The currency code for which the exchange rate is requested.
     *
     * @return float
     *   The exchange rate as a floating-point number.
     *
     * @throws RequestFailed
     *   If the request to the API fails.
     *
     * @throws InvalidCurrency
     *   If an invalid currency code is provided.
     */
    public function getRate(string $currency): float;

    /**
     * Convert an amount from one currency to another.
     *
     * @param float|int|string $amount
     *   The amount to be converted.
     *
     * @param string $fromCurrency
     *   The source currency code.
     *
     * @param string $toCurrency
     *   The target currency code.
     *
     * @param int|null $scale
     *   (Optional) The scale for the result of the conversion. If not provided,
     *   it uses the default scale or a custom scale from the configuration.
     *
     * @return float
     *   The converted amount as a floating-point number.
     *
     * @throws RequestFailed
     *   If the request to the API fails.
     *
     * @throws InvalidCurrency
     *   If an invalid currency code is provided.
     *
     * @throws MathException
     *   If a mathematical operation fails during conversion.
     */
    public function convert(float|int|string $amount, string $fromCurrency, string $toCurrency, ?int $scale = null): float;
}

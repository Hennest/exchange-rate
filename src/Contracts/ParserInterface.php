<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Hennest\ExchangeRate\Exceptions\InvalidCurrency;

interface ParserInterface
{
    /**
     * Parse exchange rate data for the specified currencies.
     *
     * @param array<string, float|int> $exchangeRates
     *   An array of exchange rate data, where currency codes are keys and exchange rates are values.
     *
     * @param string[] $toCurrencies
     *   An array of currency codes for which exchange rates are requested.
     *
     * @return array<string, float|int>
     *   An associative array containing exchange rates for the specified currencies.
     *
     * @throws InvalidCurrency
     *   If an invalid or unsupported currency code is provided.
     */
    public function parse(array $exchangeRates, array $toCurrencies): array;
}

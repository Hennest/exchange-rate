<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Hennest\ExchangeRate\Exceptions\InvalidCurrencyException;

interface ParserInterface
{
    /**
     * Parse exchange rate data for the specified currencies.
     *
     * @param ResponseInterface $response An instance of ResponseInterface representing exchange rate data.
     *
     * @param string[]|null $toCurrencies An array of currency codes for which exchange rates are requested.
     *
     * @return array<string, float|int> An associative array containing exchange rates for the specified currencies.
     *
     * @throws InvalidCurrencyException If an invalid or unsupported currency code is provided.
     */
    public function parse(ResponseInterface $response, array|null $toCurrencies): array;
}

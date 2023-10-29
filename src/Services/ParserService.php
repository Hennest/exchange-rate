<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrency;

class ParserService implements ParserInterface
{
    /**
     * @throws InvalidCurrency
     */
    public function parse(array $exchangeRates, array $toCurrencies): array
    {
        $result = [];

        $exchangeRates = array_change_key_case($exchangeRates);

        foreach ($toCurrencies as $currency) {
            $currencyLower = mb_strtolower($currency);

            if ( ! array_key_exists($currencyLower, $exchangeRates)) {
                throw new InvalidCurrency(
                    message: "Exchange rate data for currency '$currency' is not available."
                );
            }

            $result[$currencyLower] = $exchangeRates[$currencyLower];
        }

        return $result;
    }
}

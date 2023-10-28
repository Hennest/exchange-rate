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
    public function parseExchangeRate(array $exchangeRate, array $toCurrencies): array
    {
        $result = [];

        foreach ($toCurrencies as $currency) {
            $currencyLower = mb_strtolower($currency);

            if ( ! array_key_exists($currencyLower, $exchangeRate)) {
                throw new InvalidCurrency(
                    message: "Exchange rate data for currency '$currency' is not available."
                );
            }

            $result[$currencyLower] = $exchangeRate[$currencyLower];
        }

        return $result;
    }
}

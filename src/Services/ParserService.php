<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrency;
use InvalidArgumentException;

class ParserService implements ParserInterface
{
    /**
     * @throws InvalidCurrency
     */
    public function parse(array $exchangeRates, array $toCurrencies): array
    {
        if (empty($toCurrencies)) {
            throw new InvalidArgumentException(
                message: 'The toCurrencies array cannot be empty.'
            );
        }

        $lowerExchangeRates = array_change_key_case($exchangeRates, CASE_UPPER);
        $lowerToCurrencies = array_change_key_case(array_flip($toCurrencies), CASE_UPPER);

        if ($notExist = array_diff_key($lowerToCurrencies, $lowerExchangeRates)) {
            throw new InvalidCurrency(
                message: sprintf(
                    "Exchange rate data for currencies '%s' is not available.",
                    implode(', ', array_keys($notExist))
                )
            );
        }

        return array_intersect_key(
            $lowerExchangeRates,
            $lowerToCurrencies
        );
    }
}

<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrencyException;
use InvalidArgumentException;

final class ParserService implements ParserInterface
{
    /**
     * @throws InvalidCurrencyException
     */
    public function parse(ResponseInterface $response, array $toCurrencies): array
    {
        if (empty($toCurrencies)) {
            throw new InvalidArgumentException(
                message: 'The toCurrencies array cannot be empty.'
            );
        }

        $upperExchangeRates = array_change_key_case($response->rates(), CASE_UPPER);
        $upperCurrencies = array_change_key_case(array_flip($toCurrencies), CASE_UPPER);

        if ($notExist = array_diff_key($upperCurrencies, $upperExchangeRates)) {
            throw new InvalidCurrencyException(
                message: sprintf(
                    "Exchange rate data for currencies '%s' is not available.",
                    implode(', ', array_keys($notExist))
                )
            );
        }

        return array_intersect_key(
            $upperExchangeRates,
            $upperCurrencies
        );
    }
}

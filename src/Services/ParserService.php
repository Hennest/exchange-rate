<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrencyException;

final readonly class ParserService implements ParserInterface
{
    public function __construct(
        private(set) int $case
    ) {
    }

    public function parse(ResponseInterface $response, array|null $toCurrencies = null): array
    {
        $exchangeRates = array_change_key_case(
            array: $response->rates,
            case: $this->case
        );

        if (null === $toCurrencies) {
            return $exchangeRates;
        }

        $currencies = array_change_key_case(
            array: array_flip($toCurrencies),
            case: $this->case
        );

        if ($missingCurrencies = array_diff_key($currencies, $exchangeRates)) {
            throw new InvalidCurrencyException(
                message: sprintf(
                    "Exchange rate data for currencies [%s] is not available.",
                    implode(', ', array_keys($missingCurrencies))
                )
            );
        }

        return array_intersect_key($exchangeRates, $currencies);
    }
}

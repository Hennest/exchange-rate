<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Hennest\ExchangeRate\Contracts\ParserInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Hennest\ExchangeRate\Exceptions\InvalidCurrencyException;

final readonly class ParserService implements ParserInterface
{
    public function __construct(
        private int $toCase
    ) {
    }

    public function parse(ResponseInterface $response, array|null $toCurrencies = null): array
    {
        $upperExchangeRates = array_change_key_case(
            array: $response->rates(),
            case: $this->toCase
        );

        if (null === $toCurrencies) {
            return $upperExchangeRates;
        }

        $upperCurrencies = array_change_key_case(
            array: array_flip($toCurrencies),
            case: $this->toCase
        );

        if ($missingCurrencies = array_diff_key($upperCurrencies, $upperExchangeRates)) {
            throw new InvalidCurrencyException(
                message: sprintf(
                    "Exchange rate data for currencies '%s' is not available.",
                    implode(', ', array_keys($missingCurrencies))
                )
            );
        }

        return array_intersect_key($upperExchangeRates, $upperCurrencies);
    }
}

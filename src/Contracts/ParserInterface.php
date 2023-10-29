<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Hennest\ExchangeRate\Exceptions\InvalidCurrency;

interface ParserInterface
{
    /**
     * @param array<string, float|int> $exchangeRates
     * @param string[] $toCurrencies
     * @return array<string, float|int>
     * @throws InvalidCurrency
     */
    public function parse(array $exchangeRates, array $toCurrencies): array;
}

<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Hennest\ExchangeRate\Exceptions\InvalidCurrency;

interface ParserInterface
{
    /**
     * @param array<string, string> $exchangeRate
     * @param string[] $toCurrencies
     * @return array<string, string>
     * @throws InvalidCurrency
     */
    public function parse(array $exchangeRate, array $toCurrencies): array;
}

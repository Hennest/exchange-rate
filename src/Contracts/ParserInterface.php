<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Hennest\ExchangeRate\Exceptions\InvalidCurrency;

interface ParserInterface
{
    /**
     * @throws InvalidCurrency
     */
    public function parseExchangeRate(array $exchangeRate, array $toCurrencies): array;
}

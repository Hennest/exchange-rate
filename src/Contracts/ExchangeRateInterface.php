<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Brick\Math\Exception\MathException;
use Brick\Math\Exception\RoundingNecessaryException;
use Hennest\ExchangeRate\Exceptions\InvalidCurrency;
use Hennest\ExchangeRate\Exceptions\RequestFailed;

interface ExchangeRateInterface
{
    /**
     *
     * @param string[] $currencies
     * @return array<string, string>
     * @throws RequestFailed
     * @throws InvalidCurrency
     */
    public function rates(array $currencies): array;

    /**
     * @throws RequestFailed
     * @throws InvalidCurrency
     */
    public function getRate(string $currency): float;

    /**
     * @throws RequestFailed
     * @throws InvalidCurrency
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function convert(float|int|string $amount, string $fromCurrency, string $toCurrency, ?int $scale = null): float;
}

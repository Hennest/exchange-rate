<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use BcMath\Number;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;

interface ConverterInterface
{
    /**
     * Converts an amount from one currency to another.
     *
     * @throws RoundingNecessaryException
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    public function convert(
        int|string $amount,
        int|string $fromRate,
        int|string $toRate,
        int|null $scale = null
    ): Number;
}

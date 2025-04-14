<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use BcMath\Number;

interface ConverterInterface
{
    /**
     * The scale for the result of the conversion.
     */
    public int $scale {
        get;
    }

    /**
     * Converts an amount from one currency to another.
     */
    public function convert(
        float|int|string $amount,
        float|int|string $fromRate,
        float|int|string $toRate,
        int|null $scale = null
    ): Number;
}

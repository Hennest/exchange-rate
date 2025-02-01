<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use BcMath\Number;
use Hennest\ExchangeRate\Contracts\ConverterInterface;

final readonly class ConverterService implements ConverterInterface
{
    public function __construct(
        private int $scale,
    ) {
    }

    public function convert(
        float|int|string $amount,
        float|int|string $fromRate,
        float|int|string $toRate,
        int|null $scale = null
    ): Number {
        return new Number((string) $toRate)
            ->div((string) $fromRate)
            ->mul((string) $amount)
            ->round($scale ?? $this->scale);
    }
}

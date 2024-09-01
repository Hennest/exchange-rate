<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Services;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Hennest\ExchangeRate\Contracts\ConverterInterface;

final readonly class ConverterService implements ConverterInterface
{
    public function __construct(
        private int $scale,
    ) {
    }

    public function convert(
        float|int|string $amount,
        float|int $fromRate,
        float|int $toRate,
        int|null $scale = null
    ): float {
        return BigDecimal::of($toRate)
            ->dividedBy(
                that: $fromRate,
                scale: $scale ?? $this->scale,
                roundingMode: RoundingMode::HALF_UP
            )
            ->multipliedBy($amount)
            ->toScale(
                scale: $scale ?? $this->scale,
                roundingMode: RoundingMode::HALF_UP
            )
            ->toFloat();
    }
}

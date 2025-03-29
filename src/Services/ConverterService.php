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
        $scale = $scale ?? $this->scale;

        return new Number($this->format((float) $toRate, $scale))
            ->div($this->format((float) $fromRate, $scale))
            ->mul($this->format((float) $amount, $scale))
            ->round($scale);
    }

    private function format(float $value, int $scale): string
    {
        return number_format($value, $scale, thousands_separator: '');
    }
}

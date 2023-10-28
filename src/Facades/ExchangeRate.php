<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Facades;

use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array rates(array $currencies): array
 * @method static float getRate(string $currency): float
 * @method static float convert(float|int|string $amount, string $fromCurrency, string $toCurrency, ?int $scale = null): float
 *
 * @see \Hennest\ExchangeRate\Services\ExchangeRateService;
 */
class ExchangeRate extends Facade
{
    /**
     * @return class-string
     */
    protected static function getFacadeAccessor(): string
    {
        return ExchangeRateInterface::class;
    }
}

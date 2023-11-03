<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Illuminate\Support\Carbon;

interface ResponseAssemblerInterface
{
    /**
     * Create a Response object from provided data.
     *
     * @param float[]|int[] $rates
     */
    public function create(string $baseCurrency, Carbon $date, array $rates): ResponseInterface;
}

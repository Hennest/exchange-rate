<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Assembler;

use Hennest\ExchangeRate\Contracts\ResponseAssemblerInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Hennest\ExchangeRate\Dto\Response;
use Illuminate\Support\Carbon;

final class ResponseAssembler implements ResponseAssemblerInterface
{
    /**
     * @param float[]|int[] $rates
     */
    public function create(string $baseCurrency, Carbon $date, array $rates): ResponseInterface
    {
        return new Response(
            baseCurrency: $baseCurrency,
            date: $date,
            rates: $rates
        );
    }
}

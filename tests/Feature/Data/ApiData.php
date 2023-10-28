<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Tests\Feature\Data;

use Hennest\ExchangeRate\Contracts\ApiInterface;

class ApiData implements ApiInterface
{
    public function fetchExchangeRate(): array
    {
        return [
            'usd' => 1.0,
            'eur' => 0.82,
            'gbp' => 0.72,
        ];
    }
}

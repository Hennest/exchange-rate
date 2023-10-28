<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Hennest\ExchangeRate\Exceptions\RequestFailed;

interface ApiInterface
{
    /**
     * @throws RequestFailed
     */
    public function fetchExchangeRate(): array;
}

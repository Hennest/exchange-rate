<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Hennest\ExchangeRate\Exceptions\RequestFailed;

interface ApiInterface
{
    /**
     * @return array<string, string>
     * @throws RequestFailed
     */
    public function fetch(): array;
}

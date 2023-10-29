<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Hennest\ExchangeRate\Exceptions\RequestFailed;

interface ApiInterface
{
    /**
     * Fetch exchange rate data from the Currency API.
     *
     * @return array<string, float|int>
     *   An array containing exchange rate data with currency codes as keys and exchange rates as values.
     *
     * @throws RequestFailed
     *   If the request to the API fails.
     */
    public function fetch(): array;
}

<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

interface ApiInterface
{
    /**
     * Get the base currency code.
     */
    public function baseCurrency(): string;

    /**
     * Fetch exchange rate data from the API.
     *
     * @return ResponseInterface An array containing exchange rate data with currency codes as keys and exchange rates as values.
     *
     * @throws ConnectionException If the request to the API fails.
     * @throws RequestException If the request to the API fails.
     */
    public function fetch(): ResponseInterface;
}

<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Illuminate\Http\Client\RequestException;

interface ApiInterface
{
    /**
     * Fetch exchange rate data from the Currency API.
     *
     * @return ResponseInterface
     *   An array containing exchange rate data with currency codes as keys and exchange rates as values.
     *
     * @throws RequestException
     *   If the request to the API fails.
     */
    public function fetch(): ResponseInterface;
}

<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Drivers;

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;

/**
 * @see https://github.com/fawazahmed0/currency-api
 */
class CurrencyApiService implements ApiInterface
{
    protected const API_URL_TEMPLATE = 'https://raw.githubusercontent.com/fawazahmed0/currency-api/1/latest/currencies/%s.min.json';

    public function __construct(
        protected HttpFactory $http,
        protected string $baseCurrency,
    ) {
        $this->baseCurrency = mb_strtolower(
            $baseCurrency
        );
    }

    protected function buildApiUrl(): string
    {
        return sprintf(
            self::API_URL_TEMPLATE,
            $this->baseCurrency,
        );
    }

    /**
     * @return array<string, float|int>
     * @throws RequestException
     */
    public function fetch(): array
    {
        return (array) $this->http
            ->get($this->buildApiUrl())
            ->throw()
            ->json(
                $this->baseCurrency
            );
    }
}

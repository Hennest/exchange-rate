<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Drivers;

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\ResponseAssemblerInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Carbon;

/**
 * @see https://github.com/fawazahmed0/exchange-api
 */
final class CurrencyApiService implements ApiInterface
{
    private const API_URL_TEMPLATE = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/%s.min.json';

    public function __construct(
        private readonly HttpFactory $http,
        private readonly ResponseAssemblerInterface $responseAssembler,
        private readonly string $baseCurrency,
    ) {
    }

    public function baseCurrency(): string
    {
        return mb_strtolower(
            $this->baseCurrency
        );
    }

    public function fetch(): ResponseInterface
    {
        $response = (array) $this->http
            ->get($this->buildApiUrl())
            ->throw()
            ->json();

        return $this->responseAssembler->create(
            baseCurrency: array_keys($response)[1],
            date: new Carbon($response['date']),
            rates: $response[$this->baseCurrency()]
        );
    }

    private function buildApiUrl(): string
    {
        return sprintf(
            self::API_URL_TEMPLATE,
            $this->baseCurrency(),
        );
    }
}

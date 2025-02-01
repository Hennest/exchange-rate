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
    private const string API_URL_TEMPLATE = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/%s.min.json';

    protected string $buildApiUrl {
        get => sprintf(
            self::API_URL_TEMPLATE,
            $this->baseCurrency,
        );
    }

    public function __construct(
        private readonly HttpFactory $http,
        private readonly ResponseAssemblerInterface $responseAssembler,
        private(set) readonly string $baseCurrency,
    ) {
    }

    public function fetch(): ResponseInterface
    {
        $response = (array) $this->http
            ->get($this->buildApiUrl)
            ->throw()
            ->json();

        $baseCurrency = array_keys($response)[1];

        return $this->responseAssembler->create(
            baseCurrency: $baseCurrency,
            date: new Carbon($response['date']),
            rates: $response[$baseCurrency]
        );
    }
}

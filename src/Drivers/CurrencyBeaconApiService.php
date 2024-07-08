<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Drivers;

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\ResponseAssemblerInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;

/**
 * @see https://currencybeacon.com
 */
final class CurrencyBeaconApiService implements ApiInterface
{
    private const API_URL_TEMPLATE = 'https://api.currencybeacon.com/v1/latest?base=%s&api_key=%s';

    public function __construct(
        private readonly HttpFactory $http,
        private readonly ResponseAssemblerInterface $responseAssembler,
        private string $baseCurrency,
        private readonly string $apiKey,
    ) {
        $this->baseCurrency = mb_strtolower(
            $baseCurrency
        );
    }

    private function buildApiUrl(): string
    {
        return sprintf(
            self::API_URL_TEMPLATE,
            $this->baseCurrency,
            $this->apiKey,
        );
    }

    /**
     * @throws RequestException
     */
    public function fetch(): ResponseInterface
    {
        $response = (array) $this->http
            ->get($this->buildApiUrl())
            ->throw()
            ->json();

        /** @var array{
         *     date: string,
         *     base: string,
         *     rates: array<string, float>,
         * } $responseData
         */
        $responseData = $response['response'];

        return $this->responseAssembler->create(
            baseCurrency: $responseData['base'],
            date: new Carbon($responseData['date']),
            rates: $responseData['rates']
        );
    }
}

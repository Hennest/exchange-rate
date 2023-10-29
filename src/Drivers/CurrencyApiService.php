<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Drivers;

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Exceptions\RequestFailed;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * @see https://github.com/fawazahmed0/currency-api
 */
class CurrencyApiService implements ApiInterface
{
    protected const RETRY_COUNT = 3;

    protected const RETRY_DELAY = 200;

    protected const API_URL_TEMPLATE = 'https://raw.githubusercontent.com/fawazahmed0/currency-api/1/latest/currencies/%s.min.json';

    protected string $baseCurrency;

    public function __construct(string $baseCurrency)
    {
        $this->baseCurrency = mb_strtolower(
            $baseCurrency
        );
    }

    private function buildApiUrl(): string
    {
        return sprintf(
            self::API_URL_TEMPLATE,
            $this->baseCurrency
        );
    }

    /**
     * @return array<string, float|int>
     * @throws RequestFailed
     */
    public function fetch(): array
    {
        try {
            return (array) Http::retry(
                times: self::RETRY_COUNT,
                sleepMilliseconds: self::RETRY_DELAY
            )
                ->get($this->buildApiUrl())
                ->throw()
                ->json(
                    $this->baseCurrency
                );
        } catch (RequestException $exception) {
            throw new RequestFailed(
                message: 'Failed to fetch exchange rates.',
                previous: $exception
            );
        } catch (ConnectionException) {
            return [
                //
            ];
        }
    }
}
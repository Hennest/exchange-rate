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
    protected const BASE_CURRENCY = 'USD';

    protected const RETRY_COUNT = 3;

    protected const RETRY_DELAY = 200;

    protected const API_URL_TEMPLATE = 'https://raw.githubusercontent.com/fawazahmed0/currency-api/1/latest/currencies/%s.min.json';

    protected string $baseCurrency;

    public function __construct(?string $baseCurrency = null)
    {
        $this->baseCurrency = $baseCurrency ?? mb_strtolower(
            config('exchange-rate.base_currency', self::BASE_CURRENCY)
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
     * @throws RequestFailed
     */
    public function fetchExchangeRate(): array
    {
        try {
            return Http::retry(
                times: self::RETRY_COUNT,
                sleepMilliseconds: self::RETRY_DELAY
            )
                ->get($this->buildApiUrl())
                ->throw()
                ->collect(
                    $this->baseCurrency
                )
                ->all();
        } catch (RequestException $exception) {
            throw new RequestFailed(
                message: 'Failed to fetch exchange rates.',
                previous: $exception
            );
        } catch (ConnectionException) {
            return config('custom.fallback_currency_rate');
        }
    }
}

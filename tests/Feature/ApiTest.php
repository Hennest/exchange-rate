<?php

declare(strict_types=1);

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Tests\Feature\Data\ApiData;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

it('fetches exchange rate with valid currency', function (): void {
    app()->bind(ApiInterface::class, fn () => new ApiData);

    $exchangeRateApi = app(ApiInterface::class);

    $exchangeRates = $exchangeRateApi->fetch();

    expect($exchangeRates)->toBe([
        'usd' => 1.0,
        'eur' => 0.82,
        'gbp' => 0.72,
    ]);
})->group('exchangeApi');

it('throws an exception if it fails to fetch exchange rate', function (): void {
    Http::fake(['*' => Http::response(null, 404)]);

    $exchangeRateApi = app(ApiInterface::class);

    expect(fn () => $exchangeRateApi->fetch())->toThrow(RequestException::class);

})->group('exchangeApi');

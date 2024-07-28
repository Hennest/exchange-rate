<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Tests;

use Hennest\ExchangeRate\Providers\ExchangeRateServiceProvider;
use Hennest\ExchangeRate\Tests\Fixtures\ApiData;
use Orchestra\Testbench\TestCase;

class ExchangeRateTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        $app['config']->set('exchange-rate.drivers', [
            'currency-test' => [
                'api' => ApiData::class,
            ],
        ]);

        $app['config']->set('exchange-rate.default_driver', 'currency-test');

        return [
            ExchangeRateServiceProvider::class,
        ];
    }
}

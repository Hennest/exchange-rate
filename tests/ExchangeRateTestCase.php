<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Tests;

use Hennest\ExchangeRate\Providers\ExchangeRateServiceProvider;
use Hennest\ExchangeRate\Tests\Feature\Data\ApiData;
use Orchestra\Testbench\TestCase;

class ExchangeRateTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        $app['config']->set('exchange-rate.services.api', ApiData::class);

        return [
            ExchangeRateServiceProvider::class,
        ];
    }
}

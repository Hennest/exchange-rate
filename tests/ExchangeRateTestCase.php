<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Tests;

use Hennest\ExchangeRate\Providers\ExchangeRateServiceProvider;
use Orchestra\Testbench\TestCase;

class ExchangeRateTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ExchangeRateServiceProvider::class,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Tests\Fixtures;

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\ResponseAssemblerInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;

final class ApiData implements ApiInterface
{
    public function __construct(protected ResponseAssemblerInterface $responseAssembler)
    {
    }

    public function baseCurrency(): string
    {
        return 'usd';
    }

    public function fetch(): ResponseInterface
    {
        return $this->responseAssembler->create(
            baseCurrency: 'usd',
            date: today(),
            rates: [
                'usd' => 1.0,
                'eur' => 0.82,
                'gbp' => 0.72,
            ]
        );
    }
}

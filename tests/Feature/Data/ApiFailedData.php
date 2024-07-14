<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Tests\Feature\Data;

use Hennest\ExchangeRate\Contracts\ApiInterface;
use Hennest\ExchangeRate\Contracts\ResponseAssemblerInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

final class ApiFailedData implements ApiInterface
{
    public function __construct(protected ResponseAssemblerInterface $responseAssembler)
    {
    }

    public function fetch(): ResponseInterface
    {
        throw new RequestException(
            new Response(new \GuzzleHttp\Psr7\Response(500))
        );
    }
}

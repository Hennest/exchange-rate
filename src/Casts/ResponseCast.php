<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Casts;

use Hennest\ExchangeRate\Contracts\ResponseAssemblerInterface;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use UnexpectedValueException;

/**
 * @implements CastsAttributes<ResponseInterface, ResponseInterface>
 */
class ResponseCast implements CastsAttributes
{
    public function __construct(protected ResponseAssemblerInterface $responseAssembler)
    {
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ResponseInterface
    {
        if ( ! is_string($value)) {
            throw new UnexpectedValueException;
        }

        /** @var null|array{
         *     base?:string,
         *     date?:string,
         *     rates?:array<float|int>
         *   } $value */
        $value = json_decode((string) $value, true);

        if ( ! is_array($value) || ! isset($value['base']) || ! isset($value['date']) || ! isset($value['rates'])) {
            throw new UnexpectedValueException;
        }

        return $this->responseAssembler->create(
            $value['base'],
            new Carbon($value['date']),
            $value['rates']
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ( ! $value instanceof ResponseInterface) {
            throw new UnexpectedValueException;
        }

        return (string) json_encode([
            'base' => $value->baseCurrency(),
            'date' => $value->date(),
            'rates' => $value->rates(),
        ]);
    }
}

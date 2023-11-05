<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Dto;

use Hennest\ExchangeRate\Casts\ResponseCast;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Illuminate\Support\Carbon;

final class Response implements ResponseInterface
{
    /**
     * @param float[]|int[] $rates
     */
    public function __construct(
        protected readonly string $baseCurrency,
        protected readonly Carbon $date,
        protected readonly array $rates,
    ) {
    }

    public function baseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function date(): Carbon
    {
        return $this->date;
    }

    /**
     * @return float[]|int[]
     */
    public function rates(): array
    {
        return $this->rates;
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public static function castUsing(array $arguments): string
    {
        return ResponseCast::class;
    }

    /**
     * @return array<string, array<float|int>|string|Carbon>
     */
    public function toArray(): array
    {
        return [
            'base' => $this->baseCurrency,
            'date' => $this->date,
            'rates' => $this->rates
        ];
    }

    public function toJson($options = 0): string
    {
        return (string) json_encode($this->toArray(), $options);
    }

    /**
     * @return array<string, array<float|int>|string|Carbon>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

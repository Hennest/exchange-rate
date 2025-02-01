<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Dto;

use Hennest\ExchangeRate\Casts\ResponseCast;
use Hennest\ExchangeRate\Contracts\ResponseInterface;
use Illuminate\Support\Carbon;

final readonly class Response implements ResponseInterface
{
    /**
     * @param float[]|int[] $rates
     */
    public function __construct(
        private(set) string $baseCurrency,
        private(set) Carbon $date,
        private(set) array $rates,
    ) {
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
            'date' => $this->date->toDateTimeString(),
            'rates' => $this->rates,
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

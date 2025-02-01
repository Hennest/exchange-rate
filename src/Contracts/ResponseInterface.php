<?php

declare(strict_types=1);

namespace Hennest\ExchangeRate\Contracts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Carbon;
use JsonSerializable;

/**
 * @extends Arrayable<string, array<float|int>|string|Carbon>
 */
interface ResponseInterface extends Arrayable, Castable, Jsonable, JsonSerializable
{
    /**
     * Get the base currency code.
     */
    public string $baseCurrency {
        get;
    }

    /**
     * Get the date for the exchange rate information.
     */
    public Carbon $date {
        get;
    }

    /**
     * Get the exchange rates as an associative array with currency codes.
     *
     * @var float[]|int[]
     */
    public array $rates {
        get;
    }

    /**
     * Get the class name for casting this object using a custom cast.
     *
     * @param array<string, mixed> $arguments
     */
    public static function castUsing(array $arguments): string;

    /**
     * Convert the Response object to an array.
     *
     * @return array<string, array<float|int>|string|Carbon>
     */
    public function toArray(): array;

    /**
     *  Convert the Response object to a JSON string.
     */
    public function toJson($options = 0): string;

    /**
     * Convert the Response object to a JSON-serializable format.
     *
     * @return array<string, array<float|int>|string|Carbon>
     */
    public function jsonSerialize(): array;
}

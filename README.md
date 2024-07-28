# Exchange Rate Library

This PHP library provides a flexible and efficient way to handle exchange rates, including fetching current rates, caching, and currency conversion. It's designed to integrate seamlessly with Laravel applications.

## Features

- Fetch exchange rates from multiple API sources
- Cache exchange rates for improved performance
- Parse and filter exchange rate data
- Perform currency conversions with configurable precision
- Easily extensible and customizable
- Laravel integration via a service provider

## Installation

You can install this library via Composer:

```bash
composer require hennest/exchange-rate
```

## Laravel Integration

This library comes with a Laravel service provider for easy integration. After installation, add the service provider to your `config/app.php` file:

```php
'providers' => [
    // Other Service Providers
    Hennest\ExchangeRate\Providers\ExchangeRateServiceProvider::class,
],
```

### Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=exchange-rate-config
```

This will create a `config/exchange-rate.php` file where you can customize the library settings.

## Usage

### Basic Usage

```php
use Hennest\ExchangeRate\Contracts\ExchangeRateInterface;

class ExampleController
{
    public function __construct(
        private ExchangeRateInterface $exchangeRate
    ) {}

    public function example()
    {
        // Get exchange rates for specific currencies
        $rates = $this->exchangeRate->rates(['EUR', 'GBP', 'JPY']);

        // Get a single exchange rate
        $rate = $this->exchangeRate->getRate('EUR');

        // Convert currency
        $convertedAmount = $this->exchangeRate->convert(100, 'USD', 'EUR');
    }
    
    public function anotherExample()
    {
        // Get exchange rates for specific currencies
        $rates = app(ExchangeRateInterface::class)->rates(['EUR', 'GBP', 'JPY']);

        // Get a single exchange rate
        $rate = app(ExchangeRateInterface::class)->getRate('EUR');

        // Convert currency
        $convertedAmount = app(ExchangeRateInterface::class)->convert(100, 'USD', 'EUR');
    }
}
```

### Configuration Options

The `config/exchange-rate.php` file allows you to customize various aspects of the library. Here's a breakdown of the available options:

```php
return [
    // Base currency for the exchange rate
    'base_currency' => env('EXCHANGE_RATE_BASE_CURRENCY', 'USD'),

    // API key for the exchange rate service
    'api_key' => env('EXCHANGE_RATE_API_KEY', ''),

    // Arbitrary Precision Calculator settings
    'math' => [
        'scale' => env('EXCHANGE_RATE_SCALE', 10),
    ],

    // Cache configuration
    'cache' => [
        'prefix' => env('EXCHANGE_RATE_CACHE_PREFIX', 'exchange_rate'),
        'driver' => env('EXCHANGE_RATE_CACHE_DRIVER', env('CACHE_STORE', 'file')),
        'ttl' => env('EXCHANGE_RATE_CACHE_TTL', 6 * 3600),
    ],

    // Builder classes for creating DTOs
    'assemblers' => [
        'response' => Hennest\ExchangeRate\Assembler\ResponseAssembler::class,
    ],

    // Customizable service classes
    'services' => [
        'api' => Hennest\ExchangeRate\Drivers\CurrencyApiService::class,
        'cache' => Hennest\ExchangeRate\Services\CacheService::class,
        'parser' => Hennest\ExchangeRate\Services\ParserService::class,
        'exchange_rate' => Hennest\ExchangeRate\Services\ExchangeRateService::class,
    ],

    // Default driver
    'default_driver' => 'currency-api',

    // Available API drivers
    'drivers' => [
        'currency-api' => [
            'api' => Hennest\ExchangeRate\Drivers\CurrencyApiService::class,
        ],
        'currency-beacon' => [
            'api' => Hennest\ExchangeRate\Drivers\CurrencyBeaconApiService::class,
        ],
    ],
];
```

#### Key Configuration Options:

- `base_currency`: Set your preferred base currency (default: USD).
- `api_key`: If your chosen API requires a key, set it here or in your `.env` file.
- `math.scale`: Set the precision for decimal calculations.
- `cache`: Configure caching options including driver, prefix, and TTL.
- `services`: Override default service implementations.
- `default_driver`: Choose the default API driver.
- `drivers`: Configure multiple API drivers for flexibility.

### Extending the Library

You can set `default_driver` to `null` and update the `services` array to extend the library's functionality. You can create custom implementations of the following interfaces:

- `ApiInterface`: For custom API integrations
- `CacheInterface`: For custom caching mechanisms
- `ParserInterface`: For custom parsing logic
- `ExchangeRateInterface`: For custom exchange rate calculations

Register your custom implementations in the `services` section of the `config/exchange-rate.php` file:

```php
'services' => [
    'api' => \App\Services\MyCustomApiService::class,
    'cache' => \App\Services\MyCustomCacheService::class,
    'parser' => \App\Services\MyCustomParserService::class,
    'exchange_rate' => \App\Services\MyCustomExchangeRateService::class,
],
```

This allows you to have full control over which services are used throughout your application.

### Multiple API Drivers

The library supports multiple API drivers. You can add new drivers in the `drivers` section and switch between them by changing the `default_driver` setting.

### Adding Custom Drivers

To add a custom driver or use a different API service, extend the library by adding a new entry to the `drivers` array in the `config/exchange-rate.php` file:

```php
'drivers' => [
    'currency-api' => [
        'api' => Hennest\ExchangeRate\Drivers\CurrencyApiService::class,
    ],
    'currency-beacon' => [
        'api' => Hennest\ExchangeRate\Drivers\CurrencyBeaconApiService::class,
    ],
    'my-custom-driver' => [
        'api' => \App\Services\MyCustomApiService::class,
    ],
],
```

### Selecting a Driver

To use your custom driver, you can either:

1. Update the `default_driver` setting in the config file:

```php
'default_driver' => 'my-custom-driver',
```

2. Or set it using an environment variable:

```
EXCHANGE_RATE_DRIVER=my-custom-driver
```

### Custom Implementations

When creating custom implementations, ensure they adhere to the following interfaces:

- `ApiInterface`: For custom API integrations
- `CacheInterface`: For custom caching mechanisms
- `ParserInterface`: For custom parsing logic
- `ExchangeRateInterface`: For custom exchange rate calculations

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This library is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

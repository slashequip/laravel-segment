# laravel-segment

[![Latest Version on Packagist](https://img.shields.io/packagist/v/octohook/laravel-segment.svg?style=flat-square)](https://packagist.org/packages/octohook/laravel-segment)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/octohook/laravel-segment/run-tests?label=tests)](https://github.com/octohook/laravel-segment/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/octohook/laravel-segment/Check%20&%20fix%20styling?label=code%20style)](https://github.com/octohook/laravel-segment/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/octohook/laravel-segment.svg?style=flat-square)](https://packagist.org/packages/octohook/laravel-segment)


Laravel Segment is an opinionated, approach to integrating Segment into your Laravel application.


## Installation

You can install the package via composer:

```bash
composer require octohook/laravel-segment
```


You can publish the config file with:
```bash
php artisan vendor:publish --provider="Octohook\LaravelSegment\LaravelSegmentServiceProvider"
```

This is the contents of the published config file:

```php
return [
    'enabled' => env('SEGMENT_ENABLED', true),

    /**
     * This is your Segment API write key. It can be
     * found under Source > Settings > Api Keys
     */
    'write_key' => env('SEGMENT_WRITE_KEY', null),

    /**
     * Should the Segment service defer all tracking
     * api calls until after the response, sending
     * everything using the bulk/batch api?
     */
    'defer' => env('SEGMENT_DEFER', false),

    /**
     * Should the Segment service be run in safe mode.
     * Safe mode will only report errors in sending
     * when safe mode is off exceptions are thrown
     */
    'safe_mode' => env('SEGMENT_SAFE_MODE', true),
];
```

## Usage

### For tracking events
```php
use Octohook\LaravelSegment\Facades\Segment;

Segment::forUser($user)->track('User Signed Up', [
    'source' => 'Product Hunt',
]);
```

### For identifying users
```php
use Octohook\LaravelSegment\Facades\Segment;

Segment::forUser($user)->identify([
    'last_logged_in' => '2021-03-24 20:05:30',
    'latest_subscription_amount' => '$24.60',
]);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Octohook](https://github.com/Octohook)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

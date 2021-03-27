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

This is the contents of the published config file, which should be located at `config/segment.php`:

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

## What is a Segment User

When we talk about a 'user' in the context of this package we mean any object that
implements the `Octohook\LaravelSegment\Contracts\CanBeIdentifiedForSegment` contract
the package comes with a trait (and the interface) you can attach to your default
User model;

```php
use Illuminate\Database\Eloquent\Model;
use Octohook\LaravelSegment\Traits\HasSegmentIdentityByKey;
use Octohook\LaravelSegment\Contracts\CanBeIdentifiedForSegment;

class User extends Model implements CanBeIdentifiedForSegment
{
    use HasSegmentIdentityByKey;
}
```

Using this trait will automagically use your users' primary key as the identifier
that is sent to Segment. Alternatively, you can implement your own instance of the
`public function getSegmentIdentifier(): string;` method on your User model and not
use the trait.

### Globally identifying users

If you are sending Segment events in multiple places through your application and
through-out a request it might make sense to globally identify a user to make it
more convenient when making tracking calls.

```php
use Octohook\LaravelSegment\Facades\Segment;

Segment::setGlobalUser($user);
```

### Globally setting context

Segment allows you to send (context)[https://segment.com/docs/connections/spec/common/#context]
with your tracking events too, you can set a global context that applies to all tracking events.

```php
use Octohook\LaravelSegment\Facades\Segment;

Segment::setGlobalContext([
    'ip' => '127.0.0.1',
    'locale' => 'en-US',
    'screen' => [
        'height' => 1080,
        'width' => 1920,
    ],
]);
```

### Here have some convenience

Laravel Segment ships with a middleware that you can apply in your HTTP Kernal that will handle
the setting of the global user and some sensible global context too. It should be simple to extend
this middleware and adjust for your needs if you want to add to the default context provided.

```php
    'api' => [
        // ... other middleware
        Octohook\LaravelSegment\Middleware\ApplySegmentGlobals::class
    ],
```

## Usage

### For tracking events
```php
use Octohook\LaravelSegment\Facades\Segment;

Segment::forUser($user)->track('User Signed Up', [
    'source' => 'Product Hunt',
]);

// If you have set a global user you can
// use the simpler provided syntax.
Segment::track('User Signed Up', [
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

// If you have set a global user you can
// use the simpler provided syntax.
Segment::identify([
    'last_logged_in' => '2021-03-24 20:05:30',
    'latest_subscription_amount' => '$24.60',
]);
```

## Testing

```bash
./vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Octohook](https://github.com/octohk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

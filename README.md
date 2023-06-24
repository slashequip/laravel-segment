# Laravel Segment

[![Latest Version on Packagist](https://img.shields.io/packagist/v/slashequip/laravel-segment.svg?style=flat-square)](https://packagist.org/packages/slashequip/laravel-segment)
[![tests](https://github.com/slashequip/laravel-segment/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/slashequip/laravel-segment/actions/workflows/run-tests.yml)
[![code style](https://github.com/slashequip/laravel-segment/actions/workflows/php-cs-fixer.yml/badge.svg?branch=main)](https://github.com/slashequip/laravel-segment/actions/workflows/php-cs-fixer.yml)
[![psalm](https://github.com/slashequip/laravel-segment/actions/workflows/psalm.yml/badge.svg?branch=main)](https://github.com/slashequip/laravel-segment/actions/workflows/psalm.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/slashequip/laravel-segment.svg?style=flat-square)](https://packagist.org/packages/slashequip/laravel-segment)

![Laravel Segment Logo Banner](https://github.com/slashequip/laravel-segment/blob/main/laravel-segment-banner.svg?raw=true)

Laravel Segment is an opinionated, approach to integrating Segment into your Laravel application.


## Installation

You can install the package via composer:

```bash
composer require slashequip/laravel-segment
```


You can publish the config file with:
```bash
php artisan vendor:publish --provider="SlashEquip\LaravelSegment\LaravelSegmentServiceProvider"
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

## Setting your write key

Your write key is the API key given to you by Segment which can be found under your PHP source settings; 
`https://app.segment.com/{your-workspace-name}/sources/{your-source-name}/settings/keys` in the Segment UI.

## What is a Segment User

When we talk about a 'user' in the context of this package we mean any object that
implements the `SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment` contract
the package comes with a trait (and the interface) you can attach to your default
User model;

```php
use Illuminate\Database\Eloquent\Model;
use SlashEquip\LaravelSegment\Traits\HasSegmentIdentityByKey;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;

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
use SlashEquip\LaravelSegment\Facades\Segment;

Segment::setGlobalUser($user);
```

### Globally setting context

Segment allows you to send [context](https://segment.com/docs/connections/spec/common/#context)
with your tracking events too, you can set a global context that applies to all tracking events.

```php
use SlashEquip\LaravelSegment\Facades\Segment;

Segment::setGlobalContext([
    'ip' => '127.0.0.1',
    'locale' => 'en-US',
    'screen' => [
        'height' => 1080,
        'width' => 1920,
    ],
]);
```

### Setting context on a per event basis

You can also set context on a per event basis. This will merge the context you provide with the global context.

```php
use SlashEquip\LaravelSegment\Facades\Segment;

// Identify user with context
Segment::forUser($user)->identify([
    'name' => 'John Doe',
], [
    'page' => [
        'path' => '/signup',
        'referrer' => 'https://producthunt.com',
    ],
]);

// Track example event with context
Segment::forUser($user)->track('User Signed Up', [
    'source' => 'Product Hunt',
], [
    'page' => [
        'path' => '/signup',
        'referrer' => 'https://producthunt.com',
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
        SlashEquip\LaravelSegment\Middleware\ApplySegmentGlobals::class
    ],
```

## Usage

### For tracking events
```php
use SlashEquip\LaravelSegment\Facades\Segment;

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
use SlashEquip\LaravelSegment\Facades\Segment;

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

## Misc

### Deferring
When you start to fire many events in your application, even 2-3 per request it can be hyper-beneficial to
turn on deferring (see config). When deferring is enabled, the service will store all of your tracking events triggered
through-out the request or process and then send them in batch after your application has responded to your user. This
happens during the Laravel termination.

### Safe mode
By default safe-mode is turned on. When safe-mode is active it will swallow any exceptions thrown when making the HTTP
request to Segmenta and report them automatically to the exception handler, allow your app to continue running. When
disabled then the exception will be thrown.

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

- [SlashEquip](https://github.com/slashequip)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

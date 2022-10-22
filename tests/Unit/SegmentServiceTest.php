<?php

use Illuminate\Http\Client\Request;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Http;
use SlashEquip\LaravelSegment\Facades\Segment;
use SlashEquip\LaravelSegment\SegmentService;
use SlashEquip\LaravelSegment\Tests\Stubs\SegmentTestUser;
use function PHPUnit\Framework\assertInstanceOf;

it('can be resolved from the container', function () {
    assertInstanceOf(SegmentService::class, app(SegmentService::class));
});

it('can track a user using the track method with global user and context', function () {
    // Given we have a user
    $user = new SegmentTestUser('abcd');

    // And we are not deferring
    setDefer();

    // And we have set a write key
    setWriteKey();

    // And we have set global user
    Segment::setGlobalUser($user);

    // And we have set global context
    Segment::setGlobalContext([
        'ip' => '127.0.0.1',
    ]);

    // And we are faking the Http facade
    Http::fake();

    // When we call the track method
    Segment::track('Something Happened', [
        'name' => 'special',
    ]);

    // Then we have made the calls to Segment
    Http::assertSent(function (Request $request) {
        return $request->hasHeader('Content-Type', 'application/json')
            && $request->hasHeader('Authorization', 'Bearer '.base64_encode('key_1234:'))
            && $request->url() === 'https://api.segment.io/v1/batch'
            && $request['context'] === ['ip' => '127.0.0.1']
            && count($request['batch']) === 1
            && arraysMatch($request['batch'][0], [
                'type' => 'track',
                'userId' => 'abcd',
                'timestamp' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
                'properties' => [
                    'name' => 'special',
                ],
                'event' => 'Something Happened',
            ]);
    });
});

it('can identify a user using the identify method with global user and context', function () {
    // Given we have a user
    $user = new SegmentTestUser('abcd');

    // And we are not deferring
    setDefer();

    // And we have set a write key
    setWriteKey();

    // And we have set global user
    Segment::setGlobalUser($user);

    // And we have set global context
    Segment::setGlobalContext([
        'ip' => '127.0.0.1',
    ]);

    // And we are faking the Http facade
    Http::fake();

    // When we call the track method
    Segment::identify([
        'has_confirmed_something' => true,
    ]);

    // Then we have made the calls to Segment
    Http::assertSent(function (Request $request) {
        return $request->hasHeader('Content-Type', 'application/json')
            && $request->hasHeader('Authorization', 'Bearer '.base64_encode('key_1234:'))
            && $request->url() === 'https://api.segment.io/v1/batch'
            && $request['context'] === ['ip' => '127.0.0.1']
            && count($request['batch']) === 1
            && arraysMatch($request['batch'][0], [
                'type' => 'identify',
                'userId' => 'abcd',
                'timestamp' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
                'traits' => [
                    'has_confirmed_something' => true,
                ],
            ]);
    });
});

it('terminates the segment service on job processed', function () {
    // Given we are spying on the service
    $service = $this->spy(SegmentService::class);

    // When we fire the job processed event
    event(new JobProcessed('default', Mockery::mock(Job::class)));

    // Then we have called the terminate method
    $service->shouldHaveReceived('terminate')
        ->once();
});

it('terminates the segment service on app terminate', function () {
    // Given we are spying on the service
    $service = $this->spy(SegmentService::class);

    // When we terminate the app
    app()->terminate();

    // Then we have called the terminate method
    $service->shouldHaveReceived('terminate')
        ->once();
});

it('can track a user using the track method for a given user', function () {
    // Given we have a user
    $user = new SegmentTestUser('abcd');

    // And we are not deferring
    setDefer();

    // And we have set a write key
    setWriteKey();

    // And we are faking the Http facade
    Http::fake();

    // When we call the track method
    Segment::forUser($user)->track('Something Happened', [
        'name' => 'special',
    ]);

    // Then we have made the calls to Segment
    Http::assertSent(function (Request $request) {
        return $request->hasHeader('Content-Type', 'application/json')
            && $request->hasHeader('Authorization', 'Bearer '.base64_encode('key_1234:'))
            && $request->url() === 'https://api.segment.io/v1/batch'
            && $request['context'] === []
            && count($request['batch']) === 1
            && arraysMatch($request['batch'][0], [
                'type' => 'track',
                'userId' => 'abcd',
                'timestamp' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
                'properties' => [
                    'name' => 'special',
                ],
                'event' => 'Something Happened',
            ]);
    });
});

it('can identify a user using the identify method for a given user', function () {
    // Given we have a user
    $user = new SegmentTestUser('abcd');

    // And we are not deferring
    setDefer();

    // And we have set a write key
    setWriteKey();

    // And we are faking the Http facade
    Http::fake();

    // When we call the track method
    Segment::forUser($user)->identify([
        'has_confirmed_something' => true,
    ]);

    // Then we have made the calls to Segment
    Http::assertSent(function (Request $request) {
        return $request->hasHeader('Content-Type', 'application/json')
            && $request->hasHeader('Authorization', 'Bearer '.base64_encode('key_1234:'))
            && $request->url() === 'https://api.segment.io/v1/batch'
            && $request['context'] === []
            && count($request['batch']) === 1
            && arraysMatch($request['batch'][0], [
                'type' => 'identify',
                'userId' => 'abcd',
                'timestamp' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
                'traits' => [
                    'has_confirmed_something' => true,
                ],
            ]);
    });
});

it('defers tracking events until terminate is called when deferred is enabled', function () {
    // Given we have a user
    $user = new SegmentTestUser('abcd');

    // And we are not deferring
    setDefer(true);

    // And we have set a write key
    setWriteKey();

    // And we are faking the Http facade
    Http::fake();

    // And we call the track method
    Segment::forUser($user)->track('Something Happened', [
        'name' => 'special',
    ]);
    Segment::forUser($user)->identify([
        'seen_email' => true,
    ]);

    // And we haven't sent anything yet
    Http::assertNothingSent();

    // When we call terminate
    Segment::terminate();

    // Then we have made the calls to Segment
    Http::assertSent(function (Request $request) {
        return $request->hasHeader('Content-Type', 'application/json')
            && $request->hasHeader('Authorization', 'Bearer '.base64_encode('key_1234:'))
            && $request->url() === 'https://api.segment.io/v1/batch'
            && $request['context'] === []
            && count($request['batch']) === 2
            && arraysMatch($request['batch'][0], [
                'type' => 'track',
                'userId' => 'abcd',
                'timestamp' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
                'properties' => [
                    'name' => 'special',
                ],
                'event' => 'Something Happened',
            ])
            && arraysMatch($request['batch'][1], [
                'type' => 'identify',
                'userId' => 'abcd',
                'timestamp' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
                'traits' => [
                    'seen_email' => true,
                ],
            ]);
    });
});

it('does not sent tracking events when not enabled', function () {
    // Given we have a user
    $user = new SegmentTestUser('abcd');

    // And we are not deferring
    setDefer();

    // And we have disabled the integration
    setEnabled(false);

    // And we are faking the Http facade
    Http::fake();

    // When we call the track method
    Segment::forUser($user)->track('Something Happened', [
        'name' => 'special',
    ]);

    // Then we have made the calls to Segment
    Http::assertNothingSent();
});

<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Octohook\LaravelSegment\Facades\Segment;
use Octohook\LaravelSegment\SegmentService;
use Octohook\LaravelSegment\Tests\Stubs\SegmentTestUser;

it('can be resolved from the container', function () {
    $this->assertInstanceOf(SegmentService::class, app(SegmentService::class));
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
        return $request->hasHeader("Content-Type", "application/json")
            && $request->hasHeader("Authorization", "Bearer " . base64_encode('key_1234'))
            && $request->url() === "https://api.segment.io/v1/batch"
            && $request['context'] === ['ip' => '127.0.0.1']
            && count($request['batch']) === 1
            && arraysMatch($request['batch'][0], [
                "type" => "track",
                "userId" => "abcd",
                "timestamp" => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
                "properties" => [
                    "name" => "special"
                ],
                "event" => "Something Happened"
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
        "has_confirmed_something" => true,
    ]);

    // Then we have made the calls to Segment
    Http::assertSent(function (Request $request) {
        return $request->hasHeader("Content-Type", "application/json")
            && $request->hasHeader("Authorization", "Bearer " . base64_encode('key_1234'))
            && $request->url() === "https://api.segment.io/v1/batch"
            && $request['context'] === ['ip' => '127.0.0.1']
            && count($request['batch']) === 1
            && arraysMatch($request['batch'][0], [
                "type" => "identify",
                "userId" => "abcd",
                "timestamp" => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
                "traits" => [
                    "has_confirmed_something" => true,
                ]
            ]);
    });
});

<?php

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Facades\Fakes\SegmentFake;
use SlashEquip\LaravelSegment\Facades\Segment;
use SlashEquip\LaravelSegment\SimpleSegmentEvent;
use SlashEquip\LaravelSegment\SimpleSegmentIdentify;

beforeEach(function () {
    $this->user = new class implements CanBeIdentifiedForSegment
    {
        public function getSegmentIdentifier(): string
        {
            return 'user';
        }
    };
});

it('can be resolved from the container', function () {
    $this->assertInstanceOf(SegmentFake::class, Segment::fake());
});

it('can test no identities were called', function () {
    Segment::fake();

    Segment::assertNothingIdentified();
});

it('can test that an identity was called', function () {
    Segment::fake();

    Segment::forUser($this->user)->identify([
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertIdentified();
    Segment::assertIdentifiedTimes(1);
});

it('can test that an identity was called immediately', function () {
    Segment::fake();

    Segment::forUser($this->user)->identifyNow([
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertIdentified();
    Segment::assertIdentifiedTimes(1);
});

it('can test that an identity was called one times', function () {
    Segment::fake();

    Segment::forUser($this->user)->identify([
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertIdentified(1);
    Segment::assertIdentifiedTimes(1);
});

it('can test that an identity was called multiple times', function () {
    Segment::fake();

    Segment::forUser($this->user)->identify([
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::forUser($this->user)->identify([
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::forUser($this->user)->identify([
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertIdentified(3);
    Segment::assertIdentifiedTimes(3);
});

it('can test that an identity was called using a closure', function () {
    Segment::fake();

    Segment::forUser($this->user)->identify([
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertIdentified(function (SimpleSegmentIdentify $identify) {
        return $identify->toSegment()->data['traits']['first_name'] === 'Lorem';
    });
});

it('can test that an identity was not called using a closure', function () {
    Segment::fake();

    Segment::forUser($this->user)->identify([
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertNotIdentified(function (SimpleSegmentIdentify $identify) {
        return $identify->toSegment()->data['traits']['first_name'] === 'Unexpected';
    });
});

it('can test that no activities were called', function () {
    Segment::fake();

    Segment::assertNothingTracked();
});

it('can test that an event was tracked', function () {
    Segment::fake();

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertTracked();
    Segment::assertTrackedTimes(1);
});

it('can test that an event was tracked immediately', function () {
    Segment::fake();

    Segment::forUser($this->user)->trackNow('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertTracked();
    Segment::assertTrackedTimes(1);
});

it('it can assert an event was tracked one time', function () {
    Segment::fake();

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertTracked(1);
    Segment::assertTrackedTimes(1);
});

it('can test that an activity was tracked multiple times', function () {
    Segment::fake();

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertTracked(3);
    Segment::assertTrackedTimes(3);
});

it('can test that an activity was tracked by using a closure', function () {
    Segment::fake();

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertTracked(function (SimpleSegmentEvent $event) {
        return $event->toSegment()->data['event'] === 'some_event';
    });
});

it('can test that an activity was not tracked by using a closure', function () {
    Segment::fake();

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertNotTracked(function (SimpleSegmentEvent $event) {
        return $event->toSegment()->data['properties']['first_name'] === 'Unexpected';
    });
});

it('can test that an activity was tracked by its event name', function () {
    Segment::fake();

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertEventTracked('some_event');
});

it('can test that an activity was not tracked by its event name', function () {
    Segment::fake();

    Segment::forUser($this->user)->track('some_event', [
        'first_name' => 'Lorem',
        'last_name' => 'Ipsum',
    ]);

    Segment::assertEventNotTracked('some_random_event');
});

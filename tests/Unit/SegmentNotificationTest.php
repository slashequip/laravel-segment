<?php

use SlashEquip\LaravelSegment\Contracts\SegmentServiceContract;
use SlashEquip\LaravelSegment\SimpleSegmentEvent;
use SlashEquip\LaravelSegment\Tests\Stubs\SegmentTestNotification;
use SlashEquip\LaravelSegment\Tests\Stubs\SegmentTestUser;

it('can send a notification to a notifiable entity', function () {
    // Given we have a notifiable entity
    $notifiable = new SegmentTestUser('123456');

    // And we are spying on the service
    $service = test()->spy(SegmentServiceContract::class);

    // When we notify the entity
    $notifiable->notify(new SegmentTestNotification(987654));

    // Then the service was called appropriately
    $service->shouldHaveReceived('push')
        ->with(Mockery::on(function ($arg) {
            if (! $arg instanceof SimpleSegmentEvent) {
                return false;
            }

            $payload = $arg->toSegment();

            return $payload->user->getSegmentIdentifier() === '123456'
                && $payload->event === 'Segment Test Notification'
                && count($payload->data) === 1
                && $payload->data['some'] === 'thing';
        }))
        ->once();
});

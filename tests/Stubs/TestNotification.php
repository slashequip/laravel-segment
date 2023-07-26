<?php

namespace SlashEquip\LaravelSegment\Tests\Stubs;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Contracts\CanNotifyViaSegment;
use SlashEquip\LaravelSegment\Notifications\SegmentChannel;
use SlashEquip\LaravelSegment\SimpleSegmentEvent;

class TestNotification extends Notification implements CanNotifyViaSegment
{
    use Notifiable;

    public function __construct(
        private int $number
    ) {
    }

    public function via(object $notifiable): array
    {
        return [SegmentChannel::class];
    }

    public function toSegment(CanBeIdentifiedForSegment $notifiable): CanBeSentToSegment
    {
        return new SimpleSegmentEvent(
            $notifiable,
            'Test notification',
            ['some' => 'thing'],
        );
    }
}

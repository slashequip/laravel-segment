<?php

namespace SlashEquip\LaravelSegment\Tests\Stubs;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Contracts\CanNotifyViaSegment;
use SlashEquip\LaravelSegment\Notifications\SegmentChannel;
use SlashEquip\LaravelSegment\SimpleSegmentEvent;

class SegmentTestNotification extends Notification implements CanNotifyViaSegment
{
    public function __construct(
        private int $number
    ) {}

    public function via(object $notifiable): array
    {
        return [SegmentChannel::class];
    }

    public function toSegment(CanBeIdentifiedForSegment $notifiable): CanBeSentToSegment
    {
        return new SimpleSegmentEvent(
            $notifiable,
            Str::of(class_basename(static::class))
                ->snake()
                ->replace('_', ' ')
                ->title(),
            ['some' => 'thing'],
        );
    }
}

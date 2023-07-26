<?php

namespace SlashEquip\LaravelSegment\Notifications;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanNotifyViaSegment;
use SlashEquip\LaravelSegment\Exceptions\NotifiableCannotBeIdentifiedForSegmentException;
use SlashEquip\LaravelSegment\Facades\Segment;

class SegmentChannel
{
    public function send(object $notifiable, CanNotifyViaSegment $notification): void
    {
        if (! $notifiable instanceof CanBeIdentifiedForSegment) {
            throw new NotifiableCannotBeIdentifiedForSegmentException();
        }

        Segment::push($notification->toSegment($notifiable));
    }
}

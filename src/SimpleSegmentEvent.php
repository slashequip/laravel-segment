<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;

class SimpleSegmentEvent implements CanBeSentToSegment
{
    /**
     * @param  array<string, mixed>  $eventData
     */
    public function __construct(
        private readonly CanBeIdentifiedForSegment $user,
        private readonly string $event,
        private readonly ?array $eventData = null,
    ) {}

    public function toSegment(): SegmentPayload
    {
        return SegmentPayload::forTrack(
            user: $this->user,
            event: $this->event,
            data: $this->eventData,
        );
    }
}

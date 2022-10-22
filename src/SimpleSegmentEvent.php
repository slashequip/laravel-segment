<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Enums\SegmentPayloadType;
use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;

class SimpleSegmentEvent implements CanBeSentToSegment
{
    public function __construct(
        private CanBeIdentifiedForSegment $user,
        private string $event,
        private ?array $eventData
    ) {
    }

    public function toSegment(): SegmentPayload
    {
        $payload = new SegmentPayload(
            $this->user,
            SegmentPayloadType::TRACK,
            $this->eventData
        );

        $payload->setEvent($this->event);

        return $payload;
    }
}

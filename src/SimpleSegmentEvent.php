<?php

namespace Octohook\LaravelSegment;

use Octohook\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use Octohook\LaravelSegment\Contracts\CanBeSentToSegment;
use Octohook\LaravelSegment\Enums\SegmentPayloadType;
use Octohook\LaravelSegment\ValueObjects\SegmentPayload;

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
            SegmentPayloadType::TRACK(),
            $this->eventData
        );

        $payload->setEvent($this->event);

        return $payload;
    }
}

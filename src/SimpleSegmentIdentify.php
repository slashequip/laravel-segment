<?php

namespace Octohook\LaravelSegment;

use Octohook\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use Octohook\LaravelSegment\Contracts\CanBeSentToSegment;
use Octohook\LaravelSegment\Enums\SegmentPayloadType;
use Octohook\LaravelSegment\ValueObjects\SegmentPayload;

class SimpleSegmentIdentify implements CanBeSentToSegment
{
    public function __construct(
        private CanBeIdentifiedForSegment $user,
        private ?array $identifyData = null
    ) {}

    public function toSegment(): SegmentPayload
    {
        return new SegmentPayload(
            $this->user,
            SegmentPayloadType::IDENTIFY(),
            $this->identifyData
        );
    }
}
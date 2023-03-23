<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Enums\SegmentPayloadType;
use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;

class SimpleSegmentIdentify implements CanBeSentToSegment
{
    public function __construct(
        private CanBeIdentifiedForSegment $user,
        private ?array $identifyData = null,
        private ?array $context = null
    ) {
    }

    public function toSegment(): SegmentPayload
    {
        return new SegmentPayload(
            $this->user,
            SegmentPayloadType::IDENTIFY(),
            $this->identifyData,
            $this->context
        );
    }
}

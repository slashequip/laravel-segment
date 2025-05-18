<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Enums\SegmentPayloadType;
use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;

class SimpleSegmentIdentify implements CanBeSentToSegment
{
    /**
     * @param  array<string, mixed>  $identifyData
     */
    public function __construct(
        private CanBeIdentifiedForSegment $user,
        private ?array $identifyData = null
    ) {}

    public function toSegment(): SegmentPayload
    {
        return new SegmentPayload(
            user: $this->user,
            type: SegmentPayloadType::Identify,
            data: $this->identifyData ?? [],
        );
    }
}

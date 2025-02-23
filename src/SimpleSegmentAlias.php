<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;

class SimpleSegmentAlias implements CanBeSentToSegment
{
    public function __construct(
        private readonly CanBeIdentifiedForSegment $previousUser,
        private readonly CanBeIdentifiedForSegment $currentUser,
    ) {}

    public function toSegment(): SegmentPayload
    {
        return SegmentPayload::forAlias(
            previousUser: $this->previousUser,
            currentUser: $this->currentUser,
        );
    }
}

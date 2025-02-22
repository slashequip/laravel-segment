<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;

class SimpleSegmentIdentify implements CanBeSentToSegment
{
    /**
     * @param  array<string, mixed>  $identifyData
     */
    public function __construct(
        private CanBeIdentifiedForSegment $user,
        private ?array $identifyData = null
    ) {
        //
    }

    public function toSegment(): SegmentPayload
    {
        return SegmentPayload::forIdentify(
            user: $this->user,
            data: $this->identifyData,
        );
    }
}

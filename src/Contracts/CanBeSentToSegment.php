<?php

namespace SlashEquip\LaravelSegment\Contracts;

use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;

interface CanBeSentToSegment
{
    public function toSegment(): SegmentPayload;
}

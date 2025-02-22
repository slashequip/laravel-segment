<?php

namespace SlashEquip\LaravelSegment\Contracts;

interface SegmentPayloadable
{
    public function toRawBatch(): array;
}
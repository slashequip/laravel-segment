<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;

class SimpleSegmentUser implements CanBeIdentifiedForSegment
{
    public function __construct(
        private string $id
    ) {}

    public function getSegmentIdentifier(): string
    {
        return $this->id;
    }
}

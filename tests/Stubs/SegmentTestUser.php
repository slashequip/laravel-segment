<?php

namespace SlashEquip\LaravelSegment\Tests\Stubs;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;

class SegmentTestUser implements CanBeIdentifiedForSegment
{
    public function __construct(
        private string $id
    ) {
    }

    public function getSegmentIdentifier(): string
    {
        return $this->id;
    }
}

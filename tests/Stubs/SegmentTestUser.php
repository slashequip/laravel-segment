<?php

namespace Octohook\LaravelSegment\Tests\Stubs;

use Octohook\LaravelSegment\Contracts\CanBeIdentifiedForSegment;

class SegmentTestUser implements CanBeIdentifiedForSegment
{
    public function __construct(
        private string $id
    ) {}

    public function getSegmentIdentifier(): string
    {
        return $this->id;
    }
}
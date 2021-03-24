<?php

namespace Octohook\LaravelSegment\Contracts;

use Octohook\LaravelSegment\ValueObjects\SegmentPayload;

interface CanBeSentToSegment
{
    public function toSegment(): SegmentPayload;
}

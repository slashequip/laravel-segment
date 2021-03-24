<?php

namespace Octohook\LaravelSegment\Contracts;

interface CanBeIdentifiedForSegment
{
    public function getSegmentIdentifier(): string;
}
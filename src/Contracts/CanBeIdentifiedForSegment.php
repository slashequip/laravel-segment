<?php

namespace SlashEquip\LaravelSegment\Contracts;

interface CanBeIdentifiedForSegment
{
    public function getSegmentIdentifier(): string;
}

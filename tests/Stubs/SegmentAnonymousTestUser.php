<?php

namespace SlashEquip\LaravelSegment\Tests\Stubs;

use Illuminate\Notifications\Notifiable;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\ShouldBeAnonymouslyIdentified;

class SegmentAnonymousTestUser implements CanBeIdentifiedForSegment, ShouldBeAnonymouslyIdentified
{
    use Notifiable;

    public function __construct(
        private string $id
    ) {}

    public function getSegmentIdentifier(): string
    {
        return $this->id;
    }
}

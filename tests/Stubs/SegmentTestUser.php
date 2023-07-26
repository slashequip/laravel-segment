<?php

namespace SlashEquip\LaravelSegment\Tests\Stubs;

use Illuminate\Notifications\Notifiable;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;

class SegmentTestUser implements CanBeIdentifiedForSegment
{
    use Notifiable;

    public function __construct(
        private string $id
    ) {
    }

    public function getSegmentIdentifier(): string
    {
        return $this->id;
    }
}

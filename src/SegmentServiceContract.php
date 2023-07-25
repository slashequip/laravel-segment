<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;

interface SegmentServiceContract
{
    public function setGlobalUser(CanBeIdentifiedForSegment $globalUser): void;

    public function setGlobalContext(array $globalContext): void;

    public function track(string $event, array $eventData = null): void;

    public function identify(array $identifyData = null): void;

    public function forUser(CanBeIdentifiedForSegment $user): PendingUserSegment;

    public function push(CanBeSentToSegment $segment): void;

    public function terminate(): void;
}

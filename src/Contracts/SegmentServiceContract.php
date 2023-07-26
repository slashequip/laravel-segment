<?php

namespace SlashEquip\LaravelSegment\Contracts;

use SlashEquip\LaravelSegment\PendingUserSegment;

interface SegmentServiceContract
{
    public function setGlobalUser(CanBeIdentifiedForSegment $globalUser): void;

    /**
     * @param  array<string, mixed>  $globalContext
     */
    public function setGlobalContext(array $globalContext): void;

    /**
     * @param  array<string, mixed>  $eventData
     */
    public function track(string $event, array $eventData = null): void;

    /**
     * @param  array<string, mixed>  $identifyData
     */
    public function identify(array $identifyData = null): void;

    public function forUser(CanBeIdentifiedForSegment $user): PendingUserSegment;

    public function push(CanBeSentToSegment $segment): void;

    public function terminate(): void;
}

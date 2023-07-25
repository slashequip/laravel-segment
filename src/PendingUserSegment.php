<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;

class PendingUserSegment
{
    private SegmentServiceContract $service;

    private CanBeIdentifiedForSegment $user;

    public function __construct(SegmentServiceContract $service, CanBeIdentifiedForSegment $user)
    {
        $this->service = $service;
        $this->user = $user;
    }

    /**
     * @param  array<string, mixed>|null  $eventData
     */
    public function track(string $event, array $eventData = null): void
    {
        $this->service->push(
            new SimpleSegmentEvent($this->user, $event, $eventData)
        );
    }

    /**
     * @param  array<string, mixed>|null  $identifyData
     */
    public function identify(array $identifyData = null): void
    {
        $this->service->push(
            new SimpleSegmentIdentify($this->user, $identifyData)
        );
    }
}

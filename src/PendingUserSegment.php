<?php

namespace SlashEquip\LaravelSegment;

use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\SegmentServiceContract;

class PendingUserSegment
{
    public function __construct(
        private SegmentServiceContract $service,
        private CanBeIdentifiedForSegment $user
    ) {}

    /**
     * @param  array<string, mixed>|null  $eventData
     */
    public function track(string $event, ?array $eventData = null): void
    {
        $this->service->push(
            new SimpleSegmentEvent($this->user, $event, $eventData)
        );
    }

    /**
     * @param  array<string, mixed>|null  $eventData
     */
    public function trackNow(string $event, ?array $eventData = null): void
    {
        $this->service->push(
            new SimpleSegmentEvent($this->user, $event, $eventData)
        );

        $this->service->terminate();
    }

    /**
     * @param  array<string, mixed>|null  $identifyData
     */
    public function identify(?array $identifyData = null): void
    {
        $this->service->push(
            new SimpleSegmentIdentify($this->user, $identifyData)
        );
    }

    /**
     * @param  array<string, mixed>|null  $identifyData
     */
    public function identifyNow(?array $identifyData = null): void
    {
        $this->service->push(
            new SimpleSegmentIdentify($this->user, $identifyData)
        );

        $this->service->terminate();
    }

    public function alias(CanBeIdentifiedForSegment $previousUser): void
    {
        $this->service->push(
            new SimpleSegmentAlias(
                $previousUser,
                $this->user
            )
        );
    }

    public function aliasNow(CanBeIdentifiedForSegment $previousUser): void
    {
        $this->service->push(
            new SimpleSegmentAlias(
                $previousUser,
                $this->user
            )
        );

        $this->service->terminate();
    }
}

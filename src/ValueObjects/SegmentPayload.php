<?php

namespace SlashEquip\LaravelSegment\ValueObjects;

use DateTime;
use DateTimeZone;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Enums\SegmentPayloadType;

class SegmentPayload
{
    public readonly DateTime $timestamp;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public readonly CanBeIdentifiedForSegment $user,
        public readonly SegmentPayloadType $type,
        public readonly ?string $event = null,
        public readonly array $data = [],
        ?DateTime $timestamp = null
    ) {
        $this->timestamp = $timestamp ?: new DateTime();
    }

    public function getDataKey(): string
    {
        return match ($this->type) {
            SegmentPayloadType::Track => 'properties',
            SegmentPayloadType::Identify => 'traits',
        };
    }
}

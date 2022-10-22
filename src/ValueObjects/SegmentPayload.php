<?php

namespace SlashEquip\LaravelSegment\ValueObjects;

use DateTime;
use DateTimeZone;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Enums\SegmentPayloadType;
use SlashEquip\LaravelSegment\Exceptions\UnsupportedSegmentPayloadTypeException;

class SegmentPayload
{
    protected ?string $event = null;

    protected ?DateTime $timestamp = null;

    /**
     * @param  null|array<int, mixed>  $data
     */
    public function __construct(
        protected CanBeIdentifiedForSegment $user,
        protected SegmentPayloadType $type,
        protected ?array $data
    ) {
    }

    public function getUser(): CanBeIdentifiedForSegment
    {
        return $this->user;
    }

    public function getType(): SegmentPayloadType
    {
        return $this->type;
    }

    /**
     * @return null|array<int, mixed>
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp ?: new DateTime();
    }

    public function setTimestamp(DateTime $timestamp): void
    {
        // Always shift timezone to UTC.
        $timestamp->setTimezone(new DateTimeZone('UTC'));

        $this->timestamp = $timestamp;
    }

    public function getDataKey(): string
    {
        if ($this->type === SegmentPayloadType::TRACK) {
            return 'properties';
        }

        if ($this->type === SegmentPayloadType::IDENTIFY) {
            return 'traits';
        }

        throw new UnsupportedSegmentPayloadTypeException();
    }

    public function getUserId(): string
    {
        return $this->user->getSegmentIdentifier();
    }
}

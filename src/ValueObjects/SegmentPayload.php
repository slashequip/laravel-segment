<?php

namespace Octohook\LaravelSegment\ValueObjects;

use DateTime;
use DateTimeZone;
use Octohook\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use Octohook\LaravelSegment\Enums\SegmentPayloadType;
use Octohook\LaravelSegment\Exceptions\UnsupportedSegmentPayloadTypeException;

class SegmentPayload
{
    /** @var string */
    protected $event;

    /** @var DateTime */
    protected $timestamp;

    public function __construct(
        protected CanBeIdentifiedForSegment $user,
        protected SegmentPayloadType $type,
        protected ?array $data
    ) {}

    public function getUser(): CanBeIdentifiedForSegment
    {
        return $this->user;
    }

    public function getType(): SegmentPayloadType
    {
        return $this->type;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function getEvent(): string
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
        if ($this->type->equals(SegmentPayloadType::TRACK())) {
            return 'properties';
        }

        if ($this->type->equals(SegmentPayloadType::IDENTIFY())) {
            return 'traits';
        }

        throw new UnsupportedSegmentPayloadTypeException();
    }

    public function getUserId(): string
    {
        return (string) $this->user->getSegmentIdentifier();
    }
}

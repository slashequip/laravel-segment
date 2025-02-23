<?php

namespace SlashEquip\LaravelSegment\ValueObjects;

use Carbon\CarbonInterface;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\ShouldBeAnonymouslyIdentified;
use SlashEquip\LaravelSegment\Enums\SegmentPayloadType;

class SegmentPayload
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public readonly array $data,
    ) {
        //
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function forIdentify(
        CanBeIdentifiedForSegment $user,
        array $data = [],
        ?CarbonInterface $timestamp = null,
    ): self {
        return new self([
            'type' => SegmentPayloadType::Identify->value,
            self::getIdKey($user) => $user->getSegmentIdentifier(),
            'traits' => $data,
            'timestamp' => self::getTimestamp($timestamp),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function forTrack(
        CanBeIdentifiedForSegment $user,
        string $event,
        array $data = [],
        ?CarbonInterface $timestamp = null,
    ): self {
        return new self([
            'type' => SegmentPayloadType::Track->value,
            self::getIdKey($user) => $user->getSegmentIdentifier(),
            'event' => $event,
            'properties' => $data,
            'timestamp' => self::getTimestamp($timestamp),
        ]);
    }

    public static function forAlias(
        CanBeIdentifiedForSegment $previousUser,
        CanBeIdentifiedForSegment $currentUser,
        ?CarbonInterface $timestamp = null,
    ): self {
        return new self([
            'type' => SegmentPayloadType::Alias->value,
            'previousId' => $previousUser->getSegmentIdentifier(),
            'userId' => $currentUser->getSegmentIdentifier(),
            'timestamp' => self::getTimestamp($timestamp),
        ]);
    }

    private static function getIdKey(
        CanBeIdentifiedForSegment $user
    ): string {
        return $user instanceof ShouldBeAnonymouslyIdentified
            ? 'anonymousId'
            : 'userId';
    }

    private static function getTimestamp(
        ?CarbonInterface $timestamp = null
    ): string {
        return ($timestamp ? $timestamp : now())->format('Y-m-d\TH:i:s\Z');
    }
}

<?php

namespace SlashEquip\LaravelSegment\Facades;

use Illuminate\Support\Facades\Facade;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\PendingUserSegment;
use SlashEquip\LaravelSegment\SegmentService;

/**
 * @method static void setGlobalUser(CanBeIdentifiedForSegment $globalUser)
 * @method static void setGlobalContext(?array $globalContext)
 * @method static void track(string $event, ?array $eventData = null)
 * @method static void identify(?array $identifyData = null)
 * @method static PendingUserSegment forUser(CanBeIdentifiedForSegment $user)
 * @method static void push(CanBeSentToSegment $segment)
 * @method static void terminate()
 */
class Segment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SegmentService::class;
    }
}

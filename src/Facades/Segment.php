<?php

namespace Octohook\LaravelSegment\Facades;

use Illuminate\Support\Facades\Facade;
use Octohook\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use Octohook\LaravelSegment\Contracts\CanBeSentToSegment;
use Octohook\LaravelSegment\PendingUserSegment;
use Octohook\LaravelSegment\SegmentService;

/**
 * @method static void setGlobalUser(CanBeIdentifiedForSegment $globalUser)
 * @method static void setGlobalContext(?array $globalContext)
 * @method static void track(string $event, ?array $eventData = null)
 * @method static void identify(?array $identifyData = null)
 * @method static PendingUserSegment forUser(CanBeIdentifiedForSegment $user)
 * @method static void push(CanBeSentToSegment $segment)
 */
class Segment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SegmentService::class;
    }
}

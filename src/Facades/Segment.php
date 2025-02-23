<?php

namespace SlashEquip\LaravelSegment\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Contracts\SegmentServiceContract;
use SlashEquip\LaravelSegment\Facades\Fakes\SegmentFake;
use SlashEquip\LaravelSegment\PendingUserSegment;

/**
 * @method static void setGlobalUser(CanBeIdentifiedForSegment $globalUser)
 * @method static void setGlobalContext(?array<string, mixed> $globalContext)
 * @method static void track(string $event, ?array<string, mixed> $eventData = null)
 * @method static void trackNow(string $event, ?array<string, mixed> $eventData = null)
 * @method static void identify(?array<string, mixed> $identifyData = null)
 * @method static void identifyNow(?array<string, mixed> $identifyData = null)
 * @method static void alias(CanBeIdentifiedForSegment $previousUser, ?CanBeIdentifiedForSegment $currentUser = null)
 * @method static void aliasNow(CanBeIdentifiedForSegment $previousUser)
 * @method static PendingUserSegment forUser(CanBeIdentifiedForSegment $user)
 * @method static void push(CanBeSentToSegment $segment)
 * @method static void terminate()
 * @method static void assertIdentified(Closure|int|null $callback = null)
 * @method static void assertIdentifiedTimes(int $times)
 * @method static void assertNotIdentified(Closure $callback = null)
 * @method static void assertNothingIdentified()
 * @method static void assertTracked(Closure|int|null $callback = null)
 * @method static void assertTrackedTimes(int $times)
 * @method static void assertEventTracked(string $event,Closure|int|null $callback = null)
 * @method static void assertNotTracked(Closure $callback = null)
 * @method static void assertNothingTracked()
 * @method static void assertAliased(Closure|int|null $callback = null)
 * @method static void assertAliasedTimes(int $times)
 * @method static void assertNotAliased(Closure $callback = null)
 * @method static void assertNothingAliased()
 *
 * @see \SlashEquip\LaravelSegment\SegmentService
 * @see SegmentFake
 */
class Segment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SegmentServiceContract::class;
    }

    public static function fake(): SegmentFake
    {
        return tap(new SegmentFake, function (SegmentFake $fake) {
            static::swap($fake);
        });
    }
}

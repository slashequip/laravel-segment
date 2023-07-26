<?php

namespace SlashEquip\LaravelSegment\Facades\Fakes;

use Closure;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Contracts\SegmentServiceContract;
use SlashEquip\LaravelSegment\PendingUserSegment;
use SlashEquip\LaravelSegment\SimpleSegmentEvent;
use SlashEquip\LaravelSegment\SimpleSegmentIdentify;

class SegmentFake implements SegmentServiceContract
{
    private CanBeIdentifiedForSegment $user;

    /** @var  array<string, mixed>  $context */
    private ?array $context = [];

    /** @var  array<int, SimpleSegmentEvent>  $events */
    private array $events = [];

    /** @var  array<int, SimpleSegmentIdentify>  $identities */
    private array $identities = [];

    public function setGlobalUser(CanBeIdentifiedForSegment $globalUser): void
    {
        $this->user = $globalUser;
    }

    /**
     * @param  array<string, mixed>  $globalContext
     */
    public function setGlobalContext(array $globalContext): void
    {
        $this->context = $globalContext;
    }

    /**
     * @param  array<string, mixed>  $identifyData
     */
    public function identify(?array $identifyData = []): void
    {
        $this->identities[] = new SimpleSegmentIdentify($this->user, $identifyData);
    }

    /**
     * @param  array<string, mixed>  $eventData
     */
    public function track(string $event, array $eventData = null): void
    {
        $this->events[] = new SimpleSegmentEvent($this->user, $event, $eventData);
    }

    public function forUser(CanBeIdentifiedForSegment $user): PendingUserSegment
    {
        $this->user = $user;

        return new PendingUserSegment($this, $user);
    }

    public function push(CanBeSentToSegment $segment): void
    {
        if ($segment instanceof SimpleSegmentIdentify) {
            $this->identities[] = $segment;
        }

        if ($segment instanceof SimpleSegmentEvent) {
            $this->events[] = $segment;
        }
    }

    public function terminate(): void
    {
    }

    public function assertIdentified(Closure|int $callback = null): void
    {
        if (is_numeric($callback)) {
            $this->assertIdentifiedTimes($callback);

            return;
        }

        PHPUnit::assertTrue(
            $this->identities($callback)->count() > 0,
            'The expected identities were not called.'
        );
    }

    public function assertIdentifiedTimes(int $times = 1): void
    {
        $count = collect($this->identities)->count();

        PHPUnit::assertSame(
            $times, $count,
            "The identity was called {$count} times instead of {$times} times."
        );
    }

    public function assertNotIdentified(Closure $callback = null): void
    {
        PHPUnit::assertCount(
            0, $this->identities($callback),
            'The unexpected identity was called.'
        );
    }

    public function assertNothingIdentified(): void
    {
        $identities = collect($this->identities);

        PHPUnit::assertEmpty($identities, $identities->count().' events were found unexpectedly.');
    }

    public function assertTracked(Closure|int $callback = null): void
    {
        if (is_numeric($callback)) {
            $this->assertTrackedTimes($callback);

            return;
        }

        PHPUnit::assertTrue(
            $this->events($callback)->count() > 0,
            'The expected events were not called.'
        );
    }

    public function assertTrackedTimes(int $times = 1): void
    {
        $count = collect($this->events)->count();

        PHPUnit::assertSame(
            $times, $count,
            "The event called {$count} times instead of {$times} times."
        );
    }

    public function assertEventTracked(string $event, Closure|int $callback = null): void
    {
        PHPUnit::assertTrue(
            $this->events($callback, $event)->count() > 0,
            'The expected events were not called.'
        );
    }

    public function assertNotTracked(Closure $callback = null): void
    {
        PHPUnit::assertCount(
            0, $this->events($callback),
            'The unexpected event was called.'
        );
    }

    public function assertEventNotTracked(string $event, Closure|int $callback = null): void
    {
        PHPUnit::assertCount(
            0, $this->events($callback, $event),
            'The expected events were not called.'
        );
    }

    public function assertNothingTracked(): void
    {
        $events = collect($this->events);

        PHPUnit::assertEmpty($events, $events->count().' events were found unexpectedly.');
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    private function identities(Closure $callback = null): Collection
    {
        $identities = collect($this->identities);

        if ($identities->isEmpty()) {
            return collect();
        }

        $callback = $callback ?: fn () => true;

        return $identities->filter(fn (SimpleSegmentIdentify $identity) => $callback($identity));
    }

    private function events(Closure $callback = null, string $event = null): Collection
    {
        $events = collect($this->events);

        if ($events->isEmpty()) {
            return collect();
        }

        $callback = $callback ?: fn () => true;

        return $events
            ->when($event, function (Collection $collection) use ($event) {
                return $collection->filter(function (SimpleSegmentEvent $segmentEvent) use ($event) {
                    return $segmentEvent->toSegment()->event === $event;
                });
            })
            ->filter(fn (SimpleSegmentEvent $event) => $callback($event));
    }
}

<?php

namespace SlashEquip\LaravelSegment;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Contracts\SegmentPayloadable;
use SlashEquip\LaravelSegment\Contracts\SegmentServiceContract;
use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;
use Throwable;

class SegmentService implements SegmentServiceContract
{
    const BATCH_URL = 'https://api.segment.io/v1/batch';

    private CanBeIdentifiedForSegment $globalUser;

    /** @var array<string, mixed> */
    private array $globalContext = [];

    /** @var array<int, SegmentPayloadable> */
    private array $payloads = [];

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        private readonly array $config
    ) {}

    public function setGlobalUser(CanBeIdentifiedForSegment $globalUser): void
    {
        $this->globalUser = $globalUser;
    }

    /**
     * @param  array<string, mixed>  $globalContext
     */
    public function setGlobalContext(array $globalContext): void
    {
        $this->globalContext = $globalContext;
    }

    /**
     * @param  array<string, mixed>  $eventData
     */
    public function track(string $event, ?array $eventData = null): void
    {
        $this->push(
            new SimpleSegmentEvent($this->globalUser, $event, $eventData)
        );
    }

    /**
     * @param  array<string, mixed>  $eventData
     */
    public function trackNow(string $event, ?array $eventData = null): void
    {
        $this->push(
            new SimpleSegmentEvent($this->globalUser, $event, $eventData)
        );

        $this->terminate();
    }

    /**
     * @param  array<string, mixed>  $identifyData
     */
    public function identify(?array $identifyData = null): void
    {
        $this->push(
            new SimpleSegmentIdentify($this->globalUser, $identifyData)
        );
    }

    /**
     * @param  array<string, mixed>  $identifyData
     */
    public function identifyNow(?array $identifyData = null): void
    {
        $this->push(
            new SimpleSegmentIdentify($this->globalUser, $identifyData)
        );

        $this->terminate();
    }

    public function forUser(CanBeIdentifiedForSegment $user): PendingUserSegment
    {
        return new PendingUserSegment($this, $user);
    }

    public function push(CanBeSentToSegment $segment): void
    {
        $this->payloads[] = $segment->toSegment();

        // We are not deferring so we send now!
        if (! $this->shouldDefer()) {
            $this->terminate();
        }
    }

    public function terminate(): void
    {
        if (empty($this->payloads)) {
            return;
        }

        if (! $this->isEnabled()) {
            $this->clean();

            return;
        }

        // Send the batch request.
        $response = Http::asJson()
            ->withToken(base64_encode($this->getWriteKey()))
            ->post(self::BATCH_URL, [
                'batch' => $this->getBatchData(),
                'context' => $this->globalContext,
            ]);

        // Do error handling.
        $this->handleResponseErrors($response);

        // Clean up.
        $this->clean();
    }

    /**
     * @return array<int, mixed>
     */
    protected function getBatchData(): array
    {
        return collect($this->payloads)
            ->map(fn (SegmentPayload $payload) => $payload->data)
            ->all();
    }

    protected function handleResponseErrors(Response $response): void
    {
        \rescue(
            callback: function () use ($response) {
                // If there was an error then it can be
                // thrown here and we can process.
                $response->throw();
            },
            rescue: function (Throwable $e) {
                // Cleanup and re-throw, we stop here.
                if (! $this->inSafeMode()) {
                    $this->clean();

                    throw $e;
                }

                // We report manually to prevent duplicate exceptions reports
                report($e);
            },
            report: false
        );
    }

    protected function clean(): void
    {
        $this->payloads = [];
    }

    protected function isEnabled(): bool
    {
        return filter_var($this->config['enabled'] ?? true, FILTER_VALIDATE_BOOL);
    }

    protected function getWriteKey(): string
    {
        return sprintf('%s:', $this->config['write_key'] ?? '');
    }

    protected function shouldDefer(): bool
    {
        return filter_var($this->config['defer'] ?? false, FILTER_VALIDATE_BOOL);
    }

    protected function inSafeMode(): bool
    {
        return filter_var($this->config['safe_mode'] ?? true, FILTER_VALIDATE_BOOL);
    }

    public function alias(CanBeIdentifiedForSegment $previousUser, ?CanBeIdentifiedForSegment $currentUser = null): void
    {
        $this->push(
            new SimpleSegmentAlias(
                $previousUser,
                $currentUser ?? $this->getGlobalUserOrFail()
            )
        );
    }

    public function aliasNow(CanBeIdentifiedForSegment $previousUser, ?CanBeIdentifiedForSegment $currentUser = null): void
    {
        $this->push(
            new SimpleSegmentAlias(
                $previousUser,
                $currentUser ?? $this->getGlobalUserOrFail()
            )
        );

        $this->terminate();
    }

    private function getGlobalUserOrFail(): CanBeIdentifiedForSegment
    {
        if (! isset($this->globalUser)) {
            throw new RuntimeException('No global user set and no current user provided for alias.');
        }

        return $this->globalUser;
    }
}

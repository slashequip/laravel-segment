<?php

namespace SlashEquip\LaravelSegment;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Contracts\CanBeSentToSegment;
use SlashEquip\LaravelSegment\Contracts\SegmentServiceContract;
use SlashEquip\LaravelSegment\Enums\SegmentPayloadType;
use SlashEquip\LaravelSegment\ValueObjects\SegmentPayload;
use Throwable;

class SegmentService implements SegmentServiceContract
{
    const BATCH_URL = 'https://api.segment.io/v1/batch';

    private CanBeIdentifiedForSegment $globalUser;

    /** @var array<string, mixed> */
    private array $globalContext = [];

    /** @var array<int, mixed> */
    private array $payloads = [];

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        private readonly array $config
    ) {
    }

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
    public function track(string $event, array $eventData = null): void
    {
        $this->push(
            new SimpleSegmentEvent($this->globalUser, $event, $eventData)
        );
    }

    /**
     * @param  array<string, mixed>  $identifyData
     */
    public function identify(array $identifyData = null): void
    {
        $this->push(
            new SimpleSegmentIdentify($this->globalUser, $identifyData)
        );
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
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.base64_encode($this->getWriteKey()),
            'Content-Type' => 'application/json',
        ])
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
            ->map(function (SegmentPayload $payload) {
                return $this->getDataFromPayload($payload);
            })
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDataFromPayload(SegmentPayload $payload): array
    {
        // Initial data.
        $data = [
            'type' => $payload->type->value,
            'userId' => $payload->user->getSegmentIdentifier(),
            'timestamp' => $payload->timestamp->format('Y-m-d\TH:i:s\Z'),
        ];

        // This is important, Segment will not handle empty
        // data arrays as expected and will drop the event.
        if (! empty($payload->data)) {
            $data[$payload->getDataKey()] = $payload->data;
        }

        // If it's a tracking call we need an event name!
        if ($payload->type === SegmentPayloadType::Track) {
            $data['event'] = $payload->event;
        }

        return $data;
    }

    protected function handleResponseErrors(Response $response): void
    {
        rescue(
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
}

<?php

namespace Octohook\LaravelSegment\Traits;

/**
 * @mixin Model
 */
trait HasSegmentIdentityByKey
{
    public function getSegmentIdentifier(): string
    {
        return (string) $this->getKey();
    }
}

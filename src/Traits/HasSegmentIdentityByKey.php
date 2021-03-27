<?php

namespace Octohook\LaravelSegment\Traits;

use Illuminate\Database\Eloquent\Model;

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
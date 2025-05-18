<?php

namespace SlashEquip\LaravelSegment\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 *
 * @phpstan-ignore trait.unused
 */
trait HasSegmentIdentityByKey
{
    public function getSegmentIdentifier(): string
    {
        return (string) $this->getKey();
    }
}

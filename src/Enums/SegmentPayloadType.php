<?php

namespace SlashEquip\LaravelSegment\Enums;

enum SegmentPayloadType: string
{
    case Track = 'track';
    case Identify = 'identify';
    case Alias = 'alias';
}

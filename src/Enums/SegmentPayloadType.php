<?php

namespace SlashEquip\LaravelSegment\Enums;

enum SegmentPayloadType: string
{
    case TRACK = 'track';
    case IDENTIFY = 'identify';
}

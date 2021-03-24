<?php

return [
    'enabled' => env('SEGMENT_ENABLED', true),

    /**
     * This is your Segment API write key. It can be
     * found under Source > Settings > Api Keys
     */
    'write_key' => env('SEGMENT_WRITE_KEY', null),

    /**
     * Should the Segment service defer all tracking
     * api calls until after the response, sending
     * everything using the bulk/batch api?
     */
    'defer' => env('SEGMENT_DEFER', false),

    /**
     * Should the Segment service be run in safe mode.
     * Safe mode will only report errors in sending
     * when safe mode is off exceptions are thrown
     */
    'safe_mode' => env('SEGMENT_SAFE_MODE', true),
];

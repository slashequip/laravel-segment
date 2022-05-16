<?php

namespace SlashEquip\LaravelSegment\Tests;

use SlashEquip\LaravelSegment\LaravelSegmentServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelSegmentServiceProvider::class,
        ];
    }
}

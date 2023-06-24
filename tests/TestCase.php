<?php

namespace SlashEquip\LaravelSegment\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SlashEquip\LaravelSegment\LaravelSegmentServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelSegmentServiceProvider::class,
        ];
    }
}

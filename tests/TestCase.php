<?php

namespace Octohook\LaravelSegment\Tests;

use Octohook\LaravelSegment\LaravelSegmentServiceProvider;
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

<?php

namespace SlashEquip\LaravelSegment;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use SlashEquip\LaravelSegment\Facades\Segment;

class LaravelSegmentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Setup config publishing
        $this->publishes([
            __DIR__.'/../config/segment.php' => config_path('segment.php'),
        ]);

        /**
         * Send deferred tracking events to Segment after the response has been sent.
         *
         * @psalm-suppress UndefinedInterfaceMethod
         */
        $this->app->terminating(function () {
            Segment::terminate();
        });

        // Send deferred tracking events to Segment after a job has been processed.
        Queue::after(function () {
            Segment::terminate();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register config.
        $this->mergeConfigFrom(
            __DIR__ . '/../config/segment.php',
            'segment'
        );

        // Register the Segment service.
        $this->app->singleton(SegmentService::class, function ($app) {
            return new SegmentService($app->make('config')['segment']);
        });
    }
}

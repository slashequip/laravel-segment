<?php

namespace Octohook\LaravelSegment;

use Illuminate\Support\ServiceProvider;

class LaravelSegmentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/segment.php' => config_path('segment.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/segment.php', 'segment'
        );

        $this->app->singleton(SegmentService::class, function ($app) {
            return new SegmentService($app->make('config')['segment']);
        });
    }
}

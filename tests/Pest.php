<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Octohook\LaravelSegment\Tests\TestCase;

uses(TestCase::class)->in('Unit');

function setSafeMode(bool $state = true): void
{
    config(['segment.safe_mode' => $state]);
}

function setDefer(bool $state = false): void
{
    config(['segment.defer' => $state]);
}

function setEnabled(bool $state = true): void
{
    config(['segment.enabled' => $state]);
}

function setWriteKey(string $key = 'key_1234'): void
{
    config(['segment.write_key' => $key]);
}

function arraysMatch(array $arrayOne, array $arrayTwo)
{
    array_multisort($arrayOne);
    array_multisort($arrayTwo);

    return (serialize($arrayOne) === serialize($arrayTwo));
}

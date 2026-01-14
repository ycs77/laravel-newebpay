<?php

/**
 * A helper file for VS Code Intelephense PHP extension.
 *
 * Change `$this` type hint to mixed to avoid "Undefined class property" issue.
 */

use Pest\Concerns\Expectable;
use Pest\PendingCalls\BeforeEachCall;
use Pest\PendingCalls\TestCall;
use Pest\Support\HigherOrderTapProxy;
use PHPUnit\Framework\TestCase;

/**
 * Runs the given closure before each test in the current file.
 *
 * @param-closure-this object  $closure
 *
 * @return HigherOrderTapProxy<Expectable|TestCall|TestCase>|Expectable|TestCall|TestCase|mixed
 *
 * @disregard P1075 Not all paths return a value.
 */
function beforeEach(?Closure $closure = null): BeforeEachCall
{
    //
}

/**
 * Adds the given closure as a test. The first argument
 * is the test description; the second argument is
 * a closure that contains the test expectations.
 *
 * @param-closure-this object  $closure
 *
 * @return Expectable|TestCall|TestCase|mixed
 *
 * @disregard P1075 Not all paths return a value.
 */
function test(?string $description = null, ?Closure $closure = null): HigherOrderTapProxy|TestCall
{
    //
}

/**
 * Adds the given closure as a test. The first argument
 * is the test description; the second argument is
 * a closure that contains the test expectations.
 *
 * @param-closure-this object  $closure
 *
 * @return Expectable|TestCall|TestCase|mixed
 *
 * @disregard P1075 Not all paths return a value.
 */
function it(string $description, ?Closure $closure = null): TestCall
{
    //
}

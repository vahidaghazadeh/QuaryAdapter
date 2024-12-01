<?php

namespace Opsource\QueryAdapter\Jobs\Decorators;

use Closure;
use Opsource\QueryAdapter\Jobs\ClosureBuilder;

trait AsClosure
{
    /**
     * @param  Closure  $closure
     * @return ClosureBuilder
     */
    public static function makeClosure(Closure $closure): ClosureBuilder
    {
        return (new ClosureBuilder($closure));
    }

    /**
     * @param  Closure  $closure
     * @return mixed
     * @see static::handle()
     */
    public static function runClosure(Closure $closure): static
    {
        return self::makeClosure($closure)->getClosure();
    }
}

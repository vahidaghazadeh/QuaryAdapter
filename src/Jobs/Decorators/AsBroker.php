<?php

namespace Opsource\QueryAdapter\Jobs\Decorators;

use Closure;
use Opsource\QueryAdapter\Jobs\ClosureBuilder;

trait AsBroker
{
    /**
     * @param  Closure  $closure
     * @return mixed
     * @see static::handle()
     */
    public static function runBroker(Closure $closure): static
    {
        return self::makeBroker($closure)->getClosure();
    }

    /**
     * @param  Closure  $closure
     * @return ClosureBuilder
     */
    public static function makeBroker(Closure $closure): ClosureBuilder
    {
        return (new ClosureBuilder($closure));
    }
}

<?php

namespace Opsource\QueryAdapter\Jobs\Decorators;

use Closure;
use Opsource\QueryAdapter\Jobs\ClosureBuilder;

trait AsBuiltin
{
    /**
     * @param  Closure  $closure
     * @return ClosureBuilder
     */
    public static function makeBuiltin(Closure $closure): ClosureBuilder
    {
        return (new ClosureBuilder($closure));
    }

    /**
     * @param  Closure  $closure
     * @return mixed
     * @see static::handle()
     */
    public static function runBuiltin(Closure $closure): static
    {
        return self::makeBuiltin($closure)->getClosure();
    }
}

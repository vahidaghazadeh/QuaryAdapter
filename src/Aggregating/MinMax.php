<?php

namespace Opsource\QueryAdapter\Aggregating;

class MinMax
{
    public function __construct(public mixed $min, public mixed $max)
    {
    }
}

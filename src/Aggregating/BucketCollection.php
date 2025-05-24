<?php

namespace Opsource\QueryAdapter\Aggregating;

use Illuminate\Support\Collection;

class BucketCollection extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}

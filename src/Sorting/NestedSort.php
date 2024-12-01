<?php

namespace Opsource\QueryAdapter\Sorting;

use Opsource\QueryAdapter\Contracts\DSL;
use Opsource\QueryAdapter\Contracts\IndicatorIfc;

class NestedSort implements DSL
{
    public function __construct(
        private string $path,
        private IndicatorIfc $criteria,
        private ?NestedSort $nested = null
    ) {
    }

    public function toDSL(): array
    {
        $result = ['path' => $this->path];

        if ($filter = $this->criteria->toDSL()) {
            $result['filter'] = $filter;
        }

        if ($this->nested !== null) {
            $result['nested'] = $this->nested->toDSL();
        }

        return $result;
    }
}

<?php

namespace Opsource\QueryAdapter\Filters;

use Illuminate\Support\Collection;
use Opsource\QueryAdapter\Contracts\DSL;
use Opsource\QueryAdapter\Contracts\IndicatorIfc;

class IndicatorCollection implements DSL
{
    private Collection $items;

    public function __construct()
    {
        $this->items = new Collection();
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    public function count(): int
    {
        return $this->items->count();
    }

    public function toDSL(): array
    {
        return $this->items
            ->map(fn (IndicatorIfc $criteria) => $criteria->toDSL())
            ->all();
    }

    public function add(IndicatorIfc $criteria): void
    {
        $this->items->push($criteria);
    }

    public function singleAdd(IndicatorIfc $criteria): void
    {
        $this->items->add($criteria);
    }
}

<?php

namespace Opsource\QueryAdapter\Aggregating;

use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Aggregating\Bucket\FilterAggregation;
use Opsource\QueryAdapter\Contracts\AggregationsBuilder;
use Opsource\QueryAdapter\Filters\BoolQueryBuilder;
use Opsource\QueryAdapter\Traits\ConstructsAggregations;

class CompositeAggregationBuilder implements AggregationsBuilder
{
    use ConstructsAggregations;

    public function __construct(protected string $name, protected string $path = '')
    {
        $this->boolQuery = new BoolQueryBuilder($path);
        $this->aggregations = new AggregationCollection();
    }

    public function build(): AggregationCollection
    {
        if ($this->aggregations->isEmpty() || $this->boolQuery->isEmpty()) {
            return $this->aggregations;
        }

        $filter = new FilterAggregation($this->name, $this->boolQuery, $this->aggregations);

        return AggregationCollection::fromAggregation($filter);
    }

    public function toDSL(): array
    {
        return $this->build()->toDSL();
    }

    protected function basePath(): string
    {
        return $this->path;
    }

    protected function name(): string
    {
        return $this->name;
    }
}

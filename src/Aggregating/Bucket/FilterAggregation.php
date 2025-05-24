<?php

namespace Opsource\QueryAdapter\Aggregating\Bucket;


use Opsource\QueryAdapter\Aggregating\AggregationCollection;
use Opsource\QueryAdapter\Contracts\Aggregation;
use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class FilterAggregation implements Aggregation
{
    public function __construct(
        private string $name,
        private IndicatorIfc $criteria,
        private AggregationCollection $children
    ) {
        Assert::stringNotEmpty(trim($name));
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toDSL(): array
    {
        return [$this->name => [
            'filter' => $this->criteria->toDSL(),
            'aggs' => $this->children->toDSL(),
        ]];
    }

    public function parseResults(array $response): array
    {
        return $this->children
            ->parseResults($response[$this->name] ?? [])
            ->all();
    }
}

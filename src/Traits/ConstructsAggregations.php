<?php

namespace Opsource\QueryAdapter\Traits;

use Closure;
use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Aggregating\AggregationCollection;
use Opsource\QueryAdapter\Aggregating\Bucket\FilterAggregation;
use Opsource\QueryAdapter\Aggregating\Bucket\NestedAggregation;
use Opsource\QueryAdapter\Aggregating\Bucket\TermsAggregation;
use Opsource\QueryAdapter\Aggregating\CompositeAggregationBuilder;
use Opsource\QueryAdapter\Aggregating\Metrics\CardinalityAggregation;
use Opsource\QueryAdapter\Aggregating\Metrics\MinMaxAggregation;
use Opsource\QueryAdapter\Aggregating\Metrics\ValueCountAggregation;
use Opsource\QueryAdapter\Contracts\Aggregation;
use Opsource\QueryAdapter\Contracts\Criteria;
use Opsource\QueryAdapter\Filters\BoolQueryBuilder;
use Opsource\QueryAdapter\Sorting\Sort;

trait ConstructsAggregations
{
    use DecoratesBoolQuery;
    use Path;

    protected AggregationCollection $aggregations;

    protected BoolQueryBuilder $boolQuery;

    public function terms(
        string $name,
        string $field,
        ?int $size = null,
        ?Sort $sort = null,
        ?Aggregation $composite = null,
    ): static {
        $this->aggregations->add(new TermsAggregation($name, $this->absolutePath($field), $size, $sort, $composite));

        return $this;
    }

    public function filter(string $name, Criteria $criteria, AggregationCollection $children): static
    {
        $this->aggregations->add(new FilterAggregation($name, $criteria, $children));

        return $this;
    }

    public function minmax(string $name, string $field): static
    {
        $this->aggregations->add(new MinMaxAggregation($name, $this->absolutePath($field)));

        return $this;
    }

    public function count(string $name, string $field): static
    {
        $this->aggregations->add(new ValueCountAggregation($name, $this->absolutePath($field)));

        return $this;
    }

    public function cardinality(string $name, string $field): static
    {
        $this->aggregations->add(new CardinalityAggregation($name, $this->absolutePath($field)));

        return $this;
    }

    public function nested(string $path, Closure $callback): static
    {
        $name = $this->aggregations->generateUniqueName($this->name());
        $builder = $this->createCompositeBuilder("{$name}_filter", $path);

        /** @var AggregationCollection $aggs */
        $aggs = tap($builder, $callback)->build();
        if (! $aggs->isEmpty()) {
            $nested = new NestedAggregation($name, $path, $aggs);
            $this->aggregations->merge(AggregationCollection::fromAggregation($nested));
        }

        return $this;
    }

    protected function name(): string
    {
        return '';
    }

    protected function boolQuery(): BoolQueryBuilder
    {
        return $this->boolQuery;
    }

    protected function createCompositeBuilder(?string $name = null, string $path = ''): CompositeAggregationBuilder
    {
        return new CompositeAggregationBuilder(
            $name ?? $this->aggregations->generateUniqueName(),
            $this->absolutePath($path)
        );
    }
}

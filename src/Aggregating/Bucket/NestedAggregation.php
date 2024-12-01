<?php

namespace Opsource\QueryAdapter\Aggregating\Bucket;


use Opsource\QueryAdapter\Aggregating\AggregationCollection;
use Opsource\QueryAdapter\Contracts\Aggregation;
use Webmozart\Assert\Assert;

class NestedAggregation implements Aggregation
{
    public function __construct(private string $name, private string $path, private AggregationCollection $children)
    {
        Assert::stringNotEmpty(trim($name));
        Assert::stringNotEmpty(trim($path));
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toDSL(): array
    {
        return [$this->name => [
            'nested' => ['path' => $this->path],
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

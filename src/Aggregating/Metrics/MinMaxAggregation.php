<?php

namespace Opsource\QueryAdapter\Aggregating\Metrics;

use Opsource\QueryAdapter\Aggregating\MinMax;
use Opsource\QueryAdapter\Aggregating\Result;
use Opsource\QueryAdapter\Contracts\Aggregation;
use Webmozart\Assert\Assert;

class MinMaxAggregation implements Aggregation
{
    public function __construct(protected string $name, protected string $field)
    {
        Assert::stringNotEmpty(trim($name));
        Assert::stringNotEmpty(trim($field));
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toDSL(): array
    {
        return [
            "{$this->name}_min" => ['min' => ['field' => $this->field]],
            "{$this->name}_max" => ['max' => ['field' => $this->field]],
        ];
    }

    public function parseResults(array $response): array
    {
        return [$this->name => new MinMax(
            Result::parseValue($response["{$this->name}_min"]) ?? 0,
            Result::parseValue($response["{$this->name}_max"]) ?? 0,
        )];
    }
}

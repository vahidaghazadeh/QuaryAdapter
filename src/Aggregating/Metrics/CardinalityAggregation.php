<?php

namespace Opsource\QueryAdapter\Aggregating\Metrics;

use Opsource\QueryAdapter\Aggregating\Result;
use Opsource\QueryAdapter\Contracts\Aggregation;
use Webmozart\Assert\Assert;

class CardinalityAggregation implements Aggregation
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

    public function parseResults(array $response): array
    {
        return [$this->name => Result::parseValue($response[$this->name]) ?? 0];
    }

    public function toDSL(): array
    {
        return [
            $this->name => [
                'cardinality' => [
                    'field' => $this->field,
                ],
            ],
        ];
    }
}

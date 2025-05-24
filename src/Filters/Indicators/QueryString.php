<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class QueryString implements IndicatorIfc
{
    private string $value;
    private string $operator;

    public function __construct(public array $field, string $operator, string $value)
    {
        Assert::allNotEmpty($field);
        $this->operator = trim($operator);
        $this->value = trim($value);
    }

    public function toDSL(): array
    {
        $dsl = [
            'query_string' => [
                "default_operator" => $this->operator,
                "analyze_wildcard" => true,
                "query" => $this->value,
                "fields" => $this->field
            ]
        ];
        return $dsl;
    }
}

<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class RangeBound implements IndicatorIfc
{
    private static array $operators = [
        '>' => 'gt',
        '>=' => 'gte',
        '<' => 'lt',
        '<=' => 'lte',
    ];

    private string $operator;

    public function __construct(private string $field, string $operator, private mixed $value)
    {
        Assert::stringNotEmpty(trim($field));
        Assert::oneOf($operator, array_keys(self::$operators));

        $this->operator = self::$operators[$operator];
    }

    public function toDSL(): array
    {
        return ['range' => [$this->field => [$this->operator => $this->value]]];
    }
}

<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class Nested implements IndicatorIfc
{
    public function __construct(private string $field, private IndicatorIfc $criteria)
    {
        Assert::stringNotEmpty(trim($field));
    }

    public function toDSL(): array
    {
        return [
            'nested' => [
                'path' => $this->field,
                'query' => $this->criteria->toDSL(),
            ],
        ];
    }
}

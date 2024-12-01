<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class Exists implements IndicatorIfc
{
    public function __construct(private string $field)
    {
        Assert::stringNotEmpty(trim($field));
    }

    public function toDSL(): array
    {
        return ['exists' => ['field' => $this->field]];
    }
}

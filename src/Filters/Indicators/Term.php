<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class Term implements IndicatorIfc
{
    private mixed $value;

    public function __construct(private string $field, mixed $value)
    {
        Assert::stringNotEmpty(trim($field));

        $this->value = is_array($value) ? reset($value) : $value;
    }

    public function toDSL(): array
    {
        return ['term' => [$this->field => $this->value]];
    }
}

<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Illuminate\Contracts\Support\Arrayable;
use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class Terms implements IndicatorIfc
{
    private array $values;

    public function __construct(private string $field, array|Arrayable $values)
    {
        Assert::stringNotEmpty(trim($field));

        $this->values = $values instanceof Arrayable ? $values->toArray() : $values;
    }

    public function toDSL(): array
    {
        return ['terms' => [$this->field => $this->values]];
    }
}

<?php

namespace Opsource\QueryAdapter\Filters\Indicators;


use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Opsource\QueryAdapter\Performers\WildcardOptions;

class Wildcard implements IndicatorIfc
{
    public function __construct(private string $field, private string $query, private WildcardOptions $options)
    {
    }

    public function toDSL(): array
    {
        return ['wildcard' => [
            $this->field => array_merge($this->options->toArray(), [
                'value' => $this->query,
            ]),
        ]];
    }
}

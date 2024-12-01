<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Opsource\QueryAdapter\Performers\MultiMatchOptions;

class MultiMatch implements IndicatorIfc
{
    public function __construct(private array $fields, private string $query, private MultiMatchOptions $options)
    {
    }

    public function toDSL(): array
    {
        $dsl = ['query' => $this->query];

        if ($this->fields) {
            $dsl['fields'] = $this->fields;
        }

        return ['multi_match' => array_merge($this->options->toArray(), $dsl)];
    }
}

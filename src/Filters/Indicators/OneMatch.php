<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Opsource\QueryAdapter\Performers\MatchOptions;
use Webmozart\Assert\Assert;

class OneMatch implements IndicatorIfc
{
    public function __construct(private string $field, private string $query, private MatchOptions $options)
    {
        Assert::stringNotEmpty(trim($field));
    }

    public function toDSL(): array
    {
        $body = ['query' => $this->query];

        return [
            'match' => [
                $this->field => array_merge($this->options->toArray(), $body),
            ],
        ];
    }
}

<?php

namespace Opsource\QueryAdapter\Builder;

class AdapterQueryBuilder
{
    /**
     * @var array|string[]
     */
    public array $operators = [
        // @inherited
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'like binary', 'not like', 'ilike',
        '&', '|', '^', '<<', '>>', '&~',
        'rlike', 'not rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
        // @Elastic Search
        'exist', 'regex',
    ];

    /**
     * Operator conversion.
     *
     * @var array
     */
    protected array $conversion = [
        '='  => '=',
        '!=' => 'ne',
        '<>' => 'ne',
        '<'  => 'lt',
        '<=' => 'lte',
        '>'  => 'gt',
        '>=' => 'gte',
    ];

    public function __construct()
    {
    }

}

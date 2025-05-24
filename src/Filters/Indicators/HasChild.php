<?php
namespace App\Packages\Opsource\QueryAdapter\src\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Opsource\QueryAdapter\Filters\BoolQueryBuilder;

class HasChild implements IndicatorIfc
{
    protected string $type;
    protected BoolQueryBuilder $query;

    public function __construct(string $type, BoolQueryBuilder $query)
    {
        $this->type = $type;
        $this->query = $query;
    }

    public function toDSL(): array
    {
        return [
            'has_child' => [
                'type' => $this->type,
                'query' => $this->query->toDSL(),
            ],
        ];
    }
}

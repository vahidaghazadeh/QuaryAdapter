<?php

namespace App\Packages\Opsource\QueryAdapter\src\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;

class MatchAll implements IndicatorIfc
{
    public function toDSL(): array
    {
        return [
            'match_all' => new \stdClass(),
        ];
    }
}

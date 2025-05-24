<?php

namespace App\Packages\Opsource\QueryAdapter\src\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class MatchPhrasePrefix implements IndicatorIfc
{
    private string $value;
    private string $analyzer;

    public function __construct(public string $field, string $value, string $analyzer)
    {
//        $this->analyzer = trim($analyzer);
        $this->analyzer = $analyzer;
        $this->value = trim($value);
    }

    public function toDSL(): array
    {
        return [
            'match_phrase_prefix' => [
                $this->field => [
                    'query' => $this->value,
                    'analyzer' => $this->analyzer,
                ]
            ]
        ];
    }
}

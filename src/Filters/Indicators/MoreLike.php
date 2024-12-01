<?php

namespace Opsource\QueryAdapter\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Webmozart\Assert\Assert;

class MoreLike implements IndicatorIfc
{
    private string $value;
    private string $operator;

    public function __construct(public array $field, string $operator, string $like, public $moreConfig)
    {
        Assert::allNotEmpty($field);
        $this->operator = trim($operator);
        $this->value = trim($like);
        Assert::allNotEmpty($moreConfig);
    }

    public function toDSL(): array
    {
        $dsl = [
            'more_like_this' => [
                "fields" => $this->field,
                "like" => $this->value,
            ]
        ];

        if(!is_null($this->moreConfig)){
            foreach ($this->moreConfig as $key => $value){
                $dsl['more_like_this'][$key] = $value;
            }
        }
        return $dsl;
    }
}

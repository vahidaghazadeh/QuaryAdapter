<?php

namespace Opsource\QueryAdapter\Performers;

use Opsource\QueryAdapter\Contracts\DSL;
use Opsource\QueryAdapter\Sorting\Sort;
use Webmozart\Assert\Assert;

class InnerHits implements DSL
{
    public function __construct(
        protected string $name,
        protected int $size,
        protected ?Sort $sort,
    ) {
        Assert::stringNotEmpty(trim($name));
    }

    public function toDSL(): array
    {
        $dsl = [
            'name' => $this->name,
            'size' => $this->size,
        ];

        if ($this->sort) {
            $dsl['sort'] = $this->sort->toDSL();
        }

        return $dsl;
    }
}

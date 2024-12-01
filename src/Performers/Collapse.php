<?php

namespace Opsource\QueryAdapter\Performers;

use Opsource\QueryAdapter\Contracts\DSL;
use Webmozart\Assert\Assert;

/**
 * @property string $field
 * @property InnerHits[] $innerHits
 */
class Collapse implements DSL
{
    public function __construct(
        private string $field,
        private array $innerHits = [],
    ) {
        Assert::stringNotEmpty(trim($field));
    }

    public function field(): string
    {
        return $this->field;
    }

    public function toDSL(): array
    {
        $dsl = ['field' => $this->field];

        /** @var InnerHits $innerHit */
        foreach ($this->innerHits as $innerHit) {
            $dsl['inner_hits'][] = $innerHit->toDSL();
        }

        return $dsl;
    }
}

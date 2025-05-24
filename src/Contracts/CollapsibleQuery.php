<?php

namespace Opsource\QueryAdapter\Contracts;

interface CollapsibleQuery extends BoolQuery
{
    public function collapse(string $field): static;
}

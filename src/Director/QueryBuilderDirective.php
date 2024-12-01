<?php

namespace Opsource\QueryAdapter\Director;

use Opsource\QueryAdapter\Contracts\QueryBuilderIfc;

class QueryBuilderDirective
{
    public function __construct(protected QueryBuilderIfc $builder)
    {
    }

    public function build()
    {
        $this->builder->get();
    }
}

<?php

namespace Opsource\QueryAdapter\Contracts;


interface QueryIfc
{
    public function builder(QueryBuilderIfc $query);
}

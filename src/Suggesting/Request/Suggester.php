<?php

namespace Opsource\QueryAdapter\Suggesting\Request;


use Opsource\QueryAdapter\Contracts\DSL;

interface Suggester extends DSL
{
    public function name(): string;
}

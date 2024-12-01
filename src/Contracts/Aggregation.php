<?php

namespace Opsource\QueryAdapter\Contracts;

interface Aggregation extends DSL
{
    public function name(): string;

    public function parseResults(array $response): array;
}

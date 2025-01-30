<?php

namespace Opsource\QueryAdapter\Contracts;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Opsource\QueryAdapter\Performers\MatchOptions;
use Opsource\QueryAdapter\Performers\MultiMatchOptions;
use Opsource\QueryAdapter\Performers\WildcardOptions;

interface BoolQuery
{
    public function where(string $field, mixed $operator, mixed $value = null): static;
    public function like(array $field, mixed $value, string $operator = 'OR'): static ;
    public function moreLike(array $field, mixed $value, array $more): static;
    public function matchPhrasePrefix(string $field, string $value, string $analyzer = 'standard'): static;
    public function find(mixed $value): static;
    public function nested(string $nested, Closure $filter): static;

    public function whereNot(string $field, mixed $value): static;
    public function whereTerm(string $field, mixed $value): static;
    public function whereIn(string $field, array|Arrayable $values): static;

    public function orWhereIn(string $field, array|Arrayable $values): static;

    public function whereNotIn(string $field, array|Arrayable $values): static;

    public function whereHas(string $nested, Closure $filter): static;

    public function whereDoesntHave(string $nested, Closure $filter): static;

    public function whereNull(string $field): static;

    public function whereNotNull(string $field): static;

    public function whereMatch(string $fields, string $query, string $operator = 'or'): static;

    public function orWhereMatch(string $field, string $query, string|MatchOptions $operator = 'or'): static;

    public function whereMultiMatch(array $fields, string $query, string|MultiMatchOptions|null $type = null): static;

    public function orWhereMultiMatch(array $fields, string $query, string|MultiMatchOptions|null $type = null): static;

    public function whereWildcard(string $field, string $query, ?WildcardOptions $options = null): static;

    public function orWhereWildcard(string $field, string $query, ?WildcardOptions $options = null): static;

    public function addMustBool(callable $fn): static;
}

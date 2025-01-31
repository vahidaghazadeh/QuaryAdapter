<?php

namespace Opsource\QueryAdapter\Traits;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\ForwardsCalls;
use Opsource\QueryAdapter\Filters\BoolQueryBuilder;
use Opsource\QueryAdapter\Performers\MatchOptions;
use Opsource\QueryAdapter\Performers\MultiMatchOptions;
use Opsource\QueryAdapter\Performers\WildcardOptions;

trait DecoratesBoolQuery
{
    use ForwardsCalls;

    abstract protected function boolQuery(): BoolQueryBuilder;

    public function where(string $field, mixed $operator, mixed $value = null): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereShould(string $field, mixed $operator, mixed $value = null): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function orWhereShould(string $field, mixed $operator, mixed $value = null): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function find(mixed $value): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function like(array $field, mixed $value, string $operator = 'or'): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function moreLike(array $field, mixed $value, array $more): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function matchPhrasePrefix(string $field, string $value, string $analyzer = 'standard'): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function nested(string $nested, Closure $filter): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());
        return $this;
    }

    public function whereNot(string $field, mixed $value): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereTerm(string $field, mixed $value): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereHas(string $nested, Closure $filter): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereDoesntHave(string $nested, Closure $filter): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereIn(string $field, array|Arrayable $values): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function orWhereIn(string $field, array|Arrayable $values): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereNotIn(string $field, array|Arrayable $values): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereNull(string $field): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereNotNull(string $field): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereMatch(string $field, string $query, string|MatchOptions $operator = 'or'): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function orWhereMatch(string $field, string $query, string|MatchOptions $operator = 'or'): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereMultiMatch(array $fields, string $query, string|MultiMatchOptions|null $type = null): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function orWhereMultiMatch(array $fields, string $query, string|MultiMatchOptions|null $type = null): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function whereWildcard(string $field, string $query, ?WildcardOptions $options = null): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function orWhereWildcard(string $field, string $query, ?WildcardOptions $options = null): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }

    public function addMustBool(callable $fn): static
    {
        $this->forwardCallTo($this->boolQuery(), __FUNCTION__, func_get_args());

        return $this;
    }
}

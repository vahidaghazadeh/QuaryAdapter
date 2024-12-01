<?php

namespace Opsource\QueryAdapter\Filters;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Opsource\QueryAdapter\Contracts\BoolQuery;
use Opsource\QueryAdapter\Contracts\IndicatorIfc;
use Opsource\QueryAdapter\Filters\Indicators\Exists;
use Opsource\QueryAdapter\Filters\Indicators\MoreLike;
use Opsource\QueryAdapter\Filters\Indicators\MultiMatch;
use Opsource\QueryAdapter\Filters\Indicators\Nested;
use Opsource\QueryAdapter\Filters\Indicators\OneMatch;
use Opsource\QueryAdapter\Filters\Indicators\QueryString;
use Opsource\QueryAdapter\Filters\Indicators\RangeBound;
use Opsource\QueryAdapter\Filters\Indicators\Term;
use Opsource\QueryAdapter\Filters\Indicators\Terms;
use Opsource\QueryAdapter\Filters\Indicators\Wildcard;
use Opsource\QueryAdapter\Performers\MatchOptions;
use Opsource\QueryAdapter\Performers\MultiMatchOptions;
use Opsource\QueryAdapter\Performers\WildcardOptions;
use Opsource\QueryAdapter\Traits\Path;
use stdClass;

class BoolQueryBuilder implements BoolQuery, IndicatorIfc
{
    use Path;

    protected IndicatorCollection $must;

    protected IndicatorCollection $should;

    protected IndicatorCollection $filter;

    protected IndicatorCollection $mustNot;

    protected IndicatorCollection $term;

    public function __construct(protected string $path = '', protected bool $emptyMatchAll = true)
    {
        $this->must = new IndicatorCollection();
        $this->should = new IndicatorCollection();
        $this->filter = new IndicatorCollection();
        $this->mustNot = new IndicatorCollection();
        $this->term = new IndicatorCollection();
    }

    public static function make(string $path = '', ?Closure $builder = null): static
    {
        $instance = new static($path);

        if ($builder !== null) {
            $builder($instance);
        }

        return $instance;
    }

    public function isEmpty(): bool
    {
        return $this->criteriasCount() === 0;
    }

    //region Building DSL
    public function toDSL(): array
    {
        $count = $this->criteriasCount();
        if ($count === 0) {
            return $this->emptyMatchAll ? ['match_all' => new stdClass()] : [];
        }

        if ($count === 1 && $this->filter->count() === 1) {
            return head($this->filter->toDSL());
        }

        $body = array_merge(
            $this->criteriasToDSL('must', $this->must),
            $this->criteriasToDSL('should', $this->should),
            $this->criteriasToDSL('filter', $this->filter),
            $this->criteriasToDSL('must_not', $this->mustNot),
            $this->criteriasToDSL('term', $this->term),
        );

        return ['bool' => $body];
    }

    protected function criteriasToDSL(string $key, IndicatorCollection $criterias): array
    {
        return $criterias->isEmpty() ? [] : [$key => $criterias->toDSL()];
    }

    protected function criteriasCount(): int
    {
        return $this->must->count() + $this->should->count() + $this->filter->count() + $this->mustNot->count() + $this->term->count();
    }

    //endregion

    public function like(array $field, mixed $value, string $operator = 'or'): static
    {
        $criteria = $this->createComparisonCriteria(field: $field, operator: $operator, value: $value);
        $this->filter->add($criteria);

        return $this;
    }

    public function moreLike(array $field, mixed $value, array $more): static
    {
        $criteria = $this->createComparisonCriteria(field: $field, operator: 'more_like', value: $value, more: $more);
        $this->filter->add($criteria);
        return $this;
    }

    public function nested(string $nested, Closure $filter): static
    {
        return $this->addNestedCriteria($nested, $filter, $this->filter);
    }

    public function range(array $field)
    {

    }

    public function where(string $field, mixed $operator, mixed $value = null): static
    {
        if ($operator === '!=') {
            return $this->whereNot($field, $value);
        }

        if (func_num_args() === 2) {
            [$operator, $value] = ['=', $operator];
        }
        $criteria = $this->createComparisonCriteria($this->absolutePath($field), $operator, $value);
        $this->filter->add($criteria);

        return $this;
    }

    /**
     * @return $this
     */
    public function find(mixed $value): static
    {
        return $this->where("id", "=", $value);
    }

    public function whereNot(string $field, mixed $value): static
    {
        $this->mustNot->add(new Term($this->absolutePath($field), $value));

        return $this;
    }

    public function whereTerm(string $field, mixed $value): static
    {
        $this->term->singleAdd(new Term($this->absolutePath($field), $value));

        return $this;
    }

    public function whereIn(string $field, array|Arrayable $values): static
    {
        $this->filter->add(new Terms($this->absolutePath($field), $values));

        return $this;
    }

    public function orWhereIn(string $field, array|Arrayable $values): static
    {
        $this->should->add(new Terms($this->absolutePath($field), $values));

        return $this;
    }

    public function whereNotIn(string $field, array|Arrayable $values): static
    {
        $this->mustNot->add(new Terms($this->absolutePath($field), $values));

        return $this;
    }

    public function whereHas(string $nested, Closure $filter): static
    {
        return $this->addNestedCriteria($nested, $filter, $this->filter);
    }

    public function whereDoesntHave(string $nested, Closure $filter): static
    {
        return $this->addNestedCriteria($nested, $filter, $this->mustNot);
    }

    public function whereNull(string $field): static
    {
        $this->mustNot->add(new Exists($this->absolutePath($field)));

        return $this;
    }

    public function whereNotNull(string $field): static
    {
        $this->filter->add(new Exists($this->absolutePath($field)));

        return $this;
    }

    public function whereMatch(string $field, string $query, string|MatchOptions $operator = 'or'): static
    {
        $this->must->add($this->makeMatch($field, $query, $operator));

        return $this;
    }

    public function orWhereMatch(string $field, string $query, string|MatchOptions $operator = 'or'): static
    {
        $this->should->add($this->makeMatch($field, $query, $operator));

        return $this;
    }

    protected function makeMatch(string $field, string $query, string|MatchOptions $operator = 'or'): OneMatch
    {
        $options = is_string($operator) ? MatchOptions::make($operator) : $operator;

        return new OneMatch($this->absolutePath($field), $query, $options);
    }

    public function whereMultiMatch(array $fields, string $query, string|MultiMatchOptions|null $type = null): static
    {
        $this->must->add($this->makeMultiMatch($fields, $query, $type));

        return $this;
    }

    public function orWhereMultiMatch(array $fields, string $query, string|MultiMatchOptions|null $type = null): static
    {
        $this->should->add($this->makeMultiMatch($fields, $query, $type));

        return $this;
    }

    protected function makeMultiMatch(
        array $fields,
        string $query,
        string|MultiMatchOptions|null $type = null
    ): MultiMatch {
        $options = is_string($type) ? MultiMatchOptions::make($type) : $type;

        $fields = array_map(
            fn (string $field) => $this->absolutePath($field),
            $fields
        );

        return new MultiMatch($fields, $query, $options ?? new MultiMatchOptions());
    }

    public function whereWildcard(string $field, string $query, ?WildcardOptions $options = null): static
    {
        $this->must->add($this->makeWildcard($field, $query, $options));

        return $this;
    }

    public function orWhereWildcard(string $field, string $query, ?WildcardOptions $options = null): static
    {
        $this->should->add($this->makeWildcard($field, $query, $options));

        return $this;
    }

    protected function makeWildcard(string $field, string $query, ?WildcardOptions $options = null): Wildcard
    {
        return new Wildcard($this->absolutePath($field), $query, $options ?: new WildcardOptions());
    }

    public function addMustBool(callable $fn): static
    {
        $this->must->add(static::make(builder: $fn));

        return $this;
    }

    protected function addNestedCriteria(string $nested, Closure $filter, IndicatorCollection $target): static
    {
        $path = $this->absolutePath($nested);
        $boolQuery = static::make($path, $filter);

        if (! $boolQuery->isEmpty()) {
            $target->add(new Nested($path, $boolQuery));
        }

        return $this;
    }

    protected function createComparisonCriteria(string|array $field, string $operator, mixed $value, array $more = null): IndicatorIfc
    {
        return match ($operator) {
            '=', '!=' => new Term($field, $value),
            'and', 'or' => new QueryString($field, $operator, $value),
            'more_like' => new MoreLike($field, $operator, $value, $more),
            default => new RangeBound($field, $operator, $value)
        };
    }

    protected function basePath(): string
    {
        return $this->path;
    }
}

<?php

namespace Opsource\QueryAdapter\Sorting;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Opsource\QueryAdapter\Contracts\SortableQuery;
use Opsource\QueryAdapter\Filters\BoolQueryBuilder;
use Opsource\QueryAdapter\Tools\SortOrder;
use Opsource\QueryAdapter\Tools\SortType;
use Opsource\QueryAdapter\Tools\Utils;
use Opsource\QueryAdapter\Traits\DecoratesBoolQuery;
use Opsource\QueryAdapter\Traits\ExtendsSort;
use Opsource\QueryAdapter\Traits\Path;


class SortBuilder implements SortableQuery
{
    use DecoratesBoolQuery;
    use ExtendsSort;
    use Path;

    private SortCollection $sorts;
    private Collection $levels;

    public function __construct(SortCollection $sorts)
    {
        $this->sorts = $sorts;
        $this->levels = new Collection();
    }

    public function sortBy(string $field, string $order = SortOrder::ASC, ?string $mode = null, ?string $missingValues = null): static
    {
        $path = $this->absolutePath($field);

        $sort = new Sort(
            $path,
            strtolower($order),
            $mode === null ? $mode : strtolower($mode),
            $this->buildNested(),
            $missingValues
        );

        $this->sorts->add($sort);

        return $this;
    }

    public function sortByScript(Utils $script, string $type = SortType::NUMBER, string $order = SortOrder::ASC): static
    {
        $sort = new Sort(
            field: '_script',
            order: $order,
            type: $type,
            script: $script,
        );

        $this->sorts->add($sort);

        return $this;
    }

    public function sortByNested(string $field, Closure $callback): static
    {
        $path = $this->absolutePath($field);
        $filter = new BoolQueryBuilder($path, false);

        $this->levels->prepend($filter, $path);

        try {
            $callback($this);
        } finally {
            $this->levels->shift();
        }

        return $this;
    }

    protected function boolQuery(): BoolQueryBuilder
    {
        return $this->levels->first();
    }

    protected function basePath(): string
    {
        return $this->levels->isNotEmpty() ? $this->levels->keys()[0] : '';
    }

    private function buildNested(): ?NestedSort
    {
        if ($this->levels->isEmpty()) {
            return null;
        }

        return $this->levels->reduce(function (?NestedSort $carry, BoolQueryBuilder $query, string $path) {
            return new NestedSort($path, $query, $carry);
        });
    }
}

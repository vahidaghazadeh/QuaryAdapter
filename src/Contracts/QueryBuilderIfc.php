<?php

namespace Opsource\QueryAdapter\Contracts;

interface QueryBuilderIfc
{
    public function find(mixed $value): static;
    public function where($column, $operator = null, $value = null, $or = false, $boost = false);
    public function orWhere($column, $operator = null, $value = null, $or = false, $boost = false);

    public function whereNull($column);

    public function whereNotNull($column);

    public function dirtyWhere($column, $operator = null, $value = null, $or = false);

    public function whereMatch($column, $value = null, $options = []);

    public function whereDoesntMatch($column, $value = null, $options = []);

    public function orWhereMatch($column, $value = null, $options = []);

    public function whereHas($column, $closure = null, $or = false, $boost = false);

    public function whereHasNull($column, $closure = null, $or = false);

    public function orWhereHasNull($column, $closure = null);

    public function orWhereHas($column, $closure, $boost = false);

    public function createNestedQuery($column, $builder, $path);

    public function rawSearch($body);

    public function getQuery();

    public function with(string ...$relations);

    public function withOut(string ...$relations);

    public function count();

    public function getAggs();

    public function getAggregationBuckets($agg_name, $agg = null);

    public function whereIn(string $column, array $values, $or = false);

    public function whereNotIn(string $column, array $values, $or = false);

    public function orWhereNotIn(string $column, array $values);

    public function orWhereIn(string $column, array $values);

    public function whereBetween(string $column, $from = null, $to = null);

    public function orWhereBetween(string $column, $from = null, $to = null);

    public function orderBy(string $column, $order = 'asc', $script = false);

    public function minScore($min_score);

    public function get();

    public function scroll($scroll_alive = '5m', $scroll_size = 500, $json = false);

    public function first($return_eloquent = false);

    public function aggregate($name, $agg);

    public function aggregateAll($name, $agg);

    public function max($column, $agg_name = null);

    public function sum($column = '', $agg_name = null, $missing_value = null, $script = null);

    public function avg($column, $agg_name = null, $missing_value = null);

    public function min($column, $agg_name = null);

    public function aggregateOn($relation, $agg, $custom_name = null);

    public function groupBy($column, $size = 10);

    public function rawResults();

    public function aggregations($key = null);

    public function limit(int $limit);

    public function offset(int $offset);

    public function page(int $page, int $records_per_page);

    public function delete($key);
}

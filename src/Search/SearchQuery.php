<?php

namespace Opsource\QueryAdapter\Search;

use Closure;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Opsource\QueryAdapter\Aggregating\AggregationCollection;
use Opsource\QueryAdapter\Contracts\Aggregation;
use Opsource\QueryAdapter\Contracts\CollapsibleQuery;
use Opsource\QueryAdapter\Contracts\SearchIndex;
use Opsource\QueryAdapter\Contracts\SortableQuery;
use Opsource\QueryAdapter\Filters\BoolQueryBuilder;
use Opsource\QueryAdapter\Performers\Collapse;
use Opsource\QueryAdapter\Sorting\SortBuilder;
use Opsource\QueryAdapter\Sorting\SortCollection;
use Opsource\QueryAdapter\Tools\SortOrder;
use Opsource\QueryAdapter\Tools\SortType;
use Opsource\QueryAdapter\Tools\Utils;
use Opsource\QueryAdapter\Traits\DecoratesBoolQuery;
use Opsource\QueryAdapter\Traits\ExtendsSort;
use Webmozart\Assert\Assert;

class SearchQuery implements CollapsibleQuery, SortableQuery
{
    use DecoratesBoolQuery;
    use ExtendsSort;

    protected BoolQueryBuilder $boolQuery;

    protected SortCollection $sorts;

    protected ?Collapse $collapse = null;

    protected ?AggregationCollection $aggregations = null;

    protected ?int $size = null;

    protected ?int $from = null;

    protected array $fields = [];

    protected array $include = [];

    protected array $exclude = [];

    public function __construct(protected SearchIndex $index)
    {
        $this->boolQuery = $this->createBoolQuery();
        $this->sorts = new SortCollection();
    }

    //region Executing
    public function get(bool $_source = false, bool $as_dsl = false, bool $_parse_source = false): Collection
    {
        if ($this->size === 0) {
            return new Collection();
        }
        $response = $this->execute(size: $this->size, from: $this->from, source:$_source, as_dsl: $as_dsl);

        if ($as_dsl) {
            return collect($response);
        }

        return $this->parseHits($response, $_parse_source);
    }

    public function paginate(int $size, int $offset = 0): Page
    {
        Assert::greaterThan($size, 0);
        Assert::greaterThanEq($offset, 0);

        $response = $this->execute(size: $size, from: $offset, totals: true);
        $hits = $this->parseHits($response);

        return new Page(
            $size,
            $hits,
            aggs: $this->aggregations?->parseResults($response['aggregations'] ?? []),
            offset: $offset,
            total: data_get($response, 'hits.total.value', 0)
        );
    }

    public function cursorPaginate(int $size, ?string $cursor = null): CursorPage
    {
        Assert::greaterThan($size, 0);

        $sorts = $this->sorts->withTiebreaker($this->index->indicator());
        $current = Cursor::decode($cursor) ?? Cursor::BOF();

        if (! $sorts->matchCursor($current)) {
            throw new InvalidArgumentException('Cursor is not suitable for current sort');
        }

        $response = $this->execute($sorts, $size, cursor: $current);
        $hits = $this->parseHits($response);

        return new CursorPage(
            $size,
            $hits,
            aggs: $this->aggregations?->parseResults($response['aggregations'] ?? []),
            current: $current->encode(),
            next: $this->findNextCursor($sorts, $size, $hits),
            previous: $this->findPreviousCursor($sorts, $size, $current)
        );
    }

    private function findNextCursor(SortCollection $sorts, int $size, Collection $hits): ?string
    {
        return $hits->count() < $size
            ? null
            : $sorts->createCursor($hits->last())?->encode();
    }

    /**
     * @throws \JsonException
     */
    private function findPreviousCursor(SortCollection $sorts, int $size, Cursor $current): ?string
    {
        if ($current->isBOF()) {
            return null;
        }

        $response = $this->execute($sorts->invert(), $size, source: false, cursor: $current);
        $hits = $this->parseHits($response);

        return $hits->count() < $size
            ? Cursor::BOF()->encode()
            : $sorts->createCursor($hits->last())?->encode();
    }

    protected function execute(
        ?SortCollection $sorts = null,
        ?int $size = 10,
        ?int $from = 1,
        bool $totals = true,
        bool $source = true,
        ?Cursor $cursor = null,
        bool $as_dsl = false
    ): array {
        $dsl = [
            'size' => $size,
            'from' => $from,
            'query' => $this->boolQuery->toDSL(),
            'track_total_hits' => $totals,
            '_source' => $this->sourceToDSL($source),
            'fields' => $source && $this->fields ? $this->fields : null,
        ];

        $sorts ??= $this->sorts;
        if (! $sorts->isEmpty()) {
            $dsl['sort'] = $sorts->toDSL();
        }

        if (! is_null($this->aggregations)) {
            $dsl['aggs'] = $this->aggregations->toDSL();
        }

        if (! is_null($this->collapse)) {
            $dsl['collapse'] = $this->collapse->toDSL();
        }

        if ($cursor !== null && ! $cursor->isBOF()) {
            $dsl['search_after'] = $cursor->toDSL();
        }

        if($as_dsl) {
            return $dsl;
        }
        return $this->index->search(array_filter($dsl));
    }

    protected function sourceToDSL(bool $source): array|bool
    {
        return $source && ! $this->fields ?
            [
                'include' => $this->include,
                'exclude' => $this->exclude,
            ] :
            false;
    }

    protected function parseHits(array $response, $_parse_source = false): Collection
    {
        $source = collect(data_get($response, 'hits.hits') ?? []);
        if ($_parse_source) {
            return $this->parseSource($response);
        }
        return $source;
    }

    protected function parseSource(array $response): Collection
    {
        return collect(array_map(function ($item) {
            return $item['_source'];
        }, data_get($response, 'hits.hits') ?? []));
        //        return collect(data_get($response, 'hits.hits') ?? []);
    }

    public function sortBy(
        string $field,
        string $order = SortOrder::ASC,
        ?string $mode = null,
        ?string $missingValues = null
    ): static {
        (new SortBuilder($this->sorts))
            ->sortBy($field, $order, $mode, $missingValues);

        return $this;
    }

    public function sortByScript(Utils $script, string $type = SortType::NUMBER, string $order = SortOrder::ASC): static
    {
        (new SortBuilder($this->sorts))
            ->sortByScript($script, $type, $order);

        return $this;
    }

    public function sortByNested(string $field, Closure $callback): static
    {
        (new SortBuilder($this->sorts))->sortByNested($field, $callback);

        return $this;
    }

    public function collapse(string $field, array $innerHits = []): static
    {
        $this->collapse = new Collapse($field, $innerHits);

        return $this;
    }

    public function addAggregations(Aggregation $aggregation): static
    {
        $this->aggregations ??= new AggregationCollection();
        $this->aggregations->add($aggregation);

        return $this;
    }

    public function take(int $count): static
    {
        Assert::greaterThanEq($count, 0);

        $this->size = $count;

        return $this;
    }

    public function select(array $include): static
    {
        array_map(Assert::stringNotEmpty(...), $include);

        $this->include = $include;

        return $this;
    }

    public function exclude(array $exclude): static
    {
        array_map(Assert::stringNotEmpty(...), $exclude);

        $this->exclude = $exclude;

        return $this;
    }

    public function skip(int $count): static
    {
        Assert::greaterThanEq($count, 0);

        $this->from = $count;

        return $this;
    }

    protected function boolQuery(): BoolQueryBuilder
    {
        return $this->boolQuery;
    }

    protected function createBoolQuery(): BoolQueryBuilder
    {
        return new BoolQueryBuilder();
    }
}

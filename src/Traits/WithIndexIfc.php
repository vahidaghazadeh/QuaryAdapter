<?php

namespace Opsource\QueryAdapter\Traits;


use Exception;
use Opsource\QueryAdapter\Aggregating\AggregationsQuery;
use Opsource\QueryAdapter\Client\ElasticClient;
use Opsource\QueryAdapter\Search\SearchQuery;
use Opsource\QueryAdapter\Suggesting\SuggestQuery;

trait WithIndexIfc
{
    private ?ElasticClient $client = null;

    /**
     * @see SearchIndex::getIndicator()
     */
    abstract public function tiebreaker(): string;

    abstract protected function indexName(): string;

    /**
     * @throws Exception
     */
    protected function settings(): array
    {
        throw new Exception("Need to redefine the method");
    }

    /**
     * @see SearchIndex::search()
     */
    public function search(array $dsl): array
    {
        return $this->resolveClient()->search($this->indexName(), $dsl);
    }

    /**
     * @see SearchIndex::search()
     */
    public function deleteByQuery(array $dsl): array
    {
        return $this->resolveClient()->deleteByQuery($this->indexName(), $dsl);
    }

    public function isCreated(): bool
    {
        return $this->resolveClient()->indicesExists($this->indexName());
    }

    public function create(): void
    {
        $this->resolveClient()->indicesCreate($this->indexName(), $this->settings());
    }

    public function bulk(array $body): array
    {
        return $this->resolveClient()->bulk($this->indexName(), $body);
    }

    public function get(int|string $id): array
    {
        return $this->resolveClient()->get($this->indexName(), $id);
    }

    public function documentDelete(int|string $id): array
    {
        return $this->resolveClient()->documentDelete($this->indexName(), $id);
    }

    public function catIndices(string $indexName, ?array $getFields = null): array
    {
        return $this->resolveClient()->catIndices($indexName, $getFields);
    }

    public function indicesDelete(string $index): array
    {
        return $this->resolveClient()->indicesDelete($index);
    }

    public function indicesRefresh(): array
    {
        return $this->resolveClient()->indicesRefresh($this->indexName());
    }

    public function indicesReloadSearchAnalyzers(): array
    {
        return $this->resolveClient()->indicesReloadSearchAnalyzers($this->indexName());
    }

    public static function query(): SearchQuery
    {
        return new SearchQuery(new static());
    }

    public static function aggregate(): AggregationsQuery
    {
        return new AggregationsQuery(new static());
    }

    public static function suggest(): SuggestQuery
    {
        return new SuggestQuery(new static());
    }

    protected function resolveClient(): ElasticClient
    {
        $this->client ??= resolve(ElasticClient::class);

        return $this->client;
    }
}

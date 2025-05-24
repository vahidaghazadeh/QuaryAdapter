<?php

namespace Opsource\QueryAdapter\Traits;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Exception;
use Http\Promise\Promise;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Aggregating\AggregationsQuery;
use Opsource\QueryAdapter\Client\ElasticClient;
use Opsource\QueryAdapter\Search\SearchQuery;
use Opsource\QueryAdapter\Suggesting\SuggestQuery;

trait InteractsWithIndex
{
    private ?ElasticClient $client = null;
    protected mixed $engineData;
    protected bool $enableLog = false;

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function bulk(array $body): array
    {
        return $this->resolveClient()->bulk($this->indexName(), $body);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function catIndices(string $indexName, ?array $getFields = null): array
    {
        return $this->resolveClient()->catIndices($indexName, $getFields);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function indicesDelete(string $index): array
    {
        return $this->resolveClient()->indicesDelete($index);
    }

    public function indicesReindex(string $index): array
    {
        return [];
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function indicesNewAlias(array $body): Elasticsearch|\Http\Promise\Promise
    {
        return $this->resolveClient()->indicesPutAlias($body);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function indicesIndex(string $index, array $setting): Elasticsearch|\Http\Promise\Promise|null
    {
        return $this->resolveClient()->indicesCreate($index, $setting);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function indicesRefresh(): array
    {
        return $this->resolveClient()->indicesRefresh($this->indexName());
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function indicesReloadSearchAnalyzers(): array
    {
        return $this->resolveClient()->indicesReloadSearchAnalyzers($this->indexName());
    }


    /**
     * @param  array  $params
     * @return Elasticsearch|Promise
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function indicesAnalyzers(array $params): Elasticsearch|\Http\Promise\Promise
    {
        return $this->resolveClient()->indicesAnalyzers($params);
    }

    public function query(): SearchQuery
    {
        return new SearchQuery($this);
    }

    public function aggregate(): AggregationsQuery
    {
        return new AggregationsQuery($this);
    }

    public function suggest(): SuggestQuery
    {
        return new SuggestQuery($this);
    }

    public function enableLog(): static
    {
        $this->enableLog = true;
        return $this;
    }

    public function disableLog(): static
    {
        $this->enableLog = false;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getQueryLogs(): \Illuminate\Support\Collection
    {
        return $this->client->getQueryLog();
    }

    /**
     * @throws Exception
     */
    protected function checkRequiredParameters(array $required, array $params): void
    {
        foreach ($required as $req) {
            if (!isset($params[$req])) {
                throw new \Exception(
                    sprintf(
                        'The parameter %s is required',
                        $req
                    )
                );
            }
        }
    }

    protected function resolveClient(): ElasticClient
    {
        $this->client ??= resolve(ElasticClient::class);
        $this->client->setIndexName($this->indexName());
        $this->client->setModel($this->model());
        $this->enableLog ? $this->client->enableQueryLog() : $this->client->disableQueryLog();
        return $this->client;
    }
}

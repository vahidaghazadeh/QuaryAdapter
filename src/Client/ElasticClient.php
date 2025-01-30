<?php

namespace Opsource\QueryAdapter\Client;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Http\Promise\Promise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Debug\QueryLog;
use Opsource\QueryAdapter\Debug\QueryLogRecord;
use Opsource\QueryAdapter\Exceptions\QueryAdapterException;
use ReflectionClass;

class ElasticClient
{
    protected string $indexName;
    protected Model $model;
    private ?QueryLog $queryLog = null;

    /**
     * @param Client $client
     */
    public function __construct(private Client $client)
    {
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function setIndexName(string $indexName): void
    {
        $this->indexName = $indexName;
    }

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return void
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(string $indexName, array $dsl): array
    {
        //        $this->queryLog?->log($indexName, $dsl);
        return $this->client
            ->search(['index' => $indexName, 'body' => $dsl])
            ->asArray();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function deleteByQuery(string $indexName, array $dsl): array
    {
        $this->queryLog?->log($indexName, $dsl);

        return $this->client
            ->deleteByQuery(['index' => $indexName, 'body' => $dsl])
            ->asArray();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function get(string $indexName, int|string $id): array
    {
        return $this->client
            ->get(['index' => $indexName, 'id' => $id])
            ->asArray();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function indicesExists(string $index): bool
    {
        return $this->client
            ->indices()
            ->exists(['index' => $index])
            ->asBool();
    }

    /**
     * @param array $body
     * @return bool
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function isExistsDocument(array $body): bool
    {
        return $this->client
            ->exists($body)
            ->asBool();
    }


    /**
     * @param string $index
     * @param array $settings
     * @return Elasticsearch|Promise
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * | You can use this method to create an index
     * | The index parameter is the name of the index
     * | The settings parameter includes index settings, mappings, which itself contains many settings, including _source and properties.
     * | For more information, you can read the official page of Elasticsearch
     */
    public function indicesCreate(string $index, array $settings): Elasticsearch|Promise
    {
        return $this->client->indices()->create([
            'index' => $index,
            'body' => $settings,
        ]);
    }

    /**
     * @return Elasticsearch
     * @template ['id','index','body']
     * @throws ServerResponseException
     * @throws MissingParameterException
     * @throws ClientResponseException
     */
    public function indicesUpdate(array $body): mixed
    {
        return $this->client->update($body);
    }


    /**
     * @return Elasticsearch
     * @template ['id','index','body']
     * @return Elasticsearch|Promise|array|Collection
     * @throws NoNodeAvailableException if all the hosts are offline
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws ServerResponseException|QueryAdapterException if the status code of response is 5xx
     * @throws MissingParameterException if a required parameter is missing
     */
    public function indicesCreateDoc(array $body): Elasticsearch|Promise|array|Collection
    {
        $this->checkRequiredParameters(['id', 'index', 'body'], $body);
        return $this->client->create($body);
    }


    /**
     * @param array $body
     * @return Elasticsearch|Promise|array|Collection
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @template ['id','index','body']
     */
    public function indicesUpdateDoc(array $body): Elasticsearch|Promise|array|Collection
    {
        return $this->client->update($body);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function indicesAlias(array $body): mixed
    {
        return $this->client->index($body);
    }

    /**
     * @param array $body
     * @return Elasticsearch|Promise
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws MissingParameterException if a required parameter is missing
     * @throws ServerResponseException if the status code of response is 5xx
     */
    public function indicesPutAlias(array $body): Elasticsearch|Promise
    {
        return $this->client->indices()->putAlias([
            'index' => $body['index'],
            'name' => $body['alias']
        ]);
    }

    /**
     * @return Elasticsearch
     * @template ['id','index','body']
     * @throws ServerResponseException
     * @throws MissingParameterException
     * @throws ClientResponseException
     */
    public function documentUpdate(array $body): mixed
    {
        return $this->client->update($body);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function documentIndex(array $body): mixed
    {
        return $this->client->index($body);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function bulk(string $index, array $body): array
    {
        return $this->client
            ->bulk(['index' => $index, 'body' => $body])
            ->asArray();
    }

    /**
     * @param string $index
     * @param int|string $id
     * @return Elasticsearch|Promise|array|Collection
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function documentDelete(string $index, int|string $id): Elasticsearch|Promise|array|Collection
    {
        return $this->client
            ->delete(['index' => $index, 'id' => $id]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function catIndices(string $indexName, ?array $getFields = null): array
    {
        $response = $this->client
            ->indices()
            ->stats(['index' => "$indexName*"])
            ->asArray();

        $results = [];
        foreach ($response['indices'] as $index => $stat) { // Change $indexName to $index
            $item = [
                'index' => $index,
                'health' => $stat['health'],
                'status' => $stat['status'],
                'uuid' => $stat['uuid'],
                'pri' => Arr::get($stat, 'primaries.shard_stats.total_count'),
                'rep' => Arr::get($stat, 'total.shard_stats.total_count'),
                'docs.count' => Arr::get($stat, 'total.docs.count'),
                'docs.deleted' => Arr::get($stat, 'total.docs.deleted'),
                'store.size' => Arr::get($stat, 'total.store.size_in_bytes'),
                'pri.store.size' => Arr::get($stat, 'primaries.store.size_in_bytes'),
            ];

            $results[] = !$getFields
                ? $item
                : Arr::only($item, $getFields);
        }

        return $results;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function indicesDelete(string $indexName): array
    {
        return $this->client
            ->indices()
            ->delete(['index' => $indexName])
            ->asArray();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function indicesRefresh(string $indexName): array
    {
        return $this->client
            ->indices()
            ->refresh(['index' => $indexName])
            ->asArray();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function indicesReloadSearchAnalyzers(string $indexName): array
    {
        return $this->client
            ->indices()
            ->reloadSearchAnalyzers(['index' => $indexName])
            ->asArray();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function indicesAnalyzers(array $params): Elasticsearch|Promise
    {
        return $this->client->indices()
            ->analyze($params);
    }

    public function enableQueryLog(): void
    {
        $this->queryLog ??= new QueryLog();
    }

    public function disableQueryLog(): void
    {
        $this->queryLog = null;
    }

    /**
     * @return Collection<int,QueryLogRecord>
     */
    public function getQueryLog(): Collection
    {
        return $this->queryLog?->all() ?? new Collection();
    }

    /**
     * @throws AuthenticationException
     * @throws \ReflectionException
     */
    public static function fromConfig(array $config): static
    {
        //        Log::debug(json_encode($config, JSON_THROW_ON_ERROR));
        //        Psr18Client::class
        $builder = new ClientBuilder();
        $builder->setHosts($config['hosts']);
        Log::debug(json_encode($config['hosts']));
//            ->setHttpClient(new $config['http_client']())
//            ->setHttpClient(new Psr18Client())
        $builder->setRetries($config['retries'] ?? 1);
        $builder->setSSLVerification($config['ssl_verification'] ?? false);

        [$username, $password] = static::resolveBasicAuthData($config);

        if (filled($username)) {
            $builder->setBasicAuthentication($username, $password);
        }

        if (filled($config['http_client'] ?? null)) {
            $httpClient = new ReflectionClass($config['http_client']);
            $client = $httpClient->newInstance();
            $builder->setHttpClient($client);
        }

        if (filled($config['http_client_options'] ?? null)) {
            $options = call_user_func_array($config['http_client_options'], []);

            $builder->setHttpClientOptions($options);
        }

        return new static($builder->build());
    }

    public static function resolveBasicAuthData(array $config): array
    {
        if (filled($config['username'] ?? null)) {
            return [$config['username'], $config['password'] ?? ''];
        }

        foreach ($config['hosts'] as $host) {
            $components = parse_url($host);
            if (filled($components['user'] ?? null)) {
                return [$components['user'], $components['pass'] ?? ''];
            }
        }

        return ['', ''];
    }

    /**
     * @param array $required
     * @param array $params
     * @return void
     * @throws QueryAdapterException
     */
    protected function checkRequiredParameters(array $required, array $params): void
    {
        foreach ($required as $req) {
            if (!isset($params[$req])) {
                throw new QueryAdapterException(
                    sprintf(
                        'The parameter %s is required',
                        $req
                    )
                );
            }
        }
    }
}

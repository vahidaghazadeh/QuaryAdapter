<?php

namespace Opsource\QueryAdapter\Performers;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Opsource\QueryAdapter\Contracts\AdapterIfc;
use Opsource\QueryAdapter\Contracts\SearchableDirectiveInterface;
use Opsource\QueryAdapter\Contracts\SearchIndex;
use Opsource\QueryAdapter\Exceptions\QueryAdapterException;
use Opsource\QueryAdapter\Jobs\ArgsBuilder;
use Opsource\QueryAdapter\Traits\HasUseJob;
use Opsource\QueryAdapter\Traits\InteractsWithIndex;
use ReflectionException;
use ReflectionMethod;

abstract class SearchEngine implements SearchIndex, AdapterIfc
{
    use InteractsWithIndex;
    use HasUseJob;

    protected string $indicator;
    protected string $indexName;
    protected Model $model;
    protected bool $preventObserver;
    protected mixed $engineData;

    public function __construct(SearchableDirectiveInterface $directive)
    {
        $this->indexName = $directive->getIndexName();
        $this->indicator = $directive->getIndicator();
        $this->model = $directive->getModel();
        $this->preventObserver = $directive->getPreventObserver();
        $this->queueArgs = new ArgsBuilder();
        $this->brokerArgs = new ArgsBuilder();
    }

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return $this->indexName();
    }

    abstract protected function indexName(): string;

    /**
     * @return string
     */
    public function getIndicator(): string
    {
        return $this->indicator();
    }

    /**
     * @see SearchIndex::tiebreaker()
     */
    abstract public function indicator(): string;

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function get(int|string|null $id = null): array
    {
        if ($this->model()->id) {
            $id = $this->model()->id;
        }
        return $this->resolveClient()->get($this->indexName(), $id);
    }

    abstract protected function model(): Model;

    //    abstract public function preventObserver(): bool;

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model();
    }

    public function isPreventObserver(): bool
    {
        return $this->preventObserver;
    }

    abstract public function getEngineData(bool $as_collect = false);

    /**
     * @throws QueryAdapterException
     */
    abstract public function setEngineData(string $method = null, ...$parameters): static;

    /**
     * @return Elasticsearch|Promise|string|array|Collection
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws MissingParameterException if a required parameter is missing
     * @throws QueryAdapterException if the status code of response is 5xx
     * @throws ServerResponseException if the status code of response is 5xx
     */
    public function createDoc(): Elasticsearch|Promise|string|array|Collection
    {
        if ($this->isPreventObserver()) {
            if (!$this->resolveClient()->isExistsDocument(
                ['id' => $this->model()->id, "index" => $this->getIndexName()]
            )) {
                $response = $this->resolveClient()->indicesCreateDoc([
                    $this->getIndicator() => $this->model()->id,
                    "index" => $this->indexName(),
                    "body" => $this->getEngineData(),
                ]);
                $this->setResponse($response);
                return $response;
            } else {
                return "doc {$this->model()->id} exist in {$this->getIndexName()}";
            }
        }
        return "Model create is disabled by preventObserver";
    }

    /**
     * @param bool $use_doc
     * @return Elasticsearch|string|array|Collection
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws QueryAdapterException
     * @throws ServerResponseException
     */
    public function updateDoc(bool $use_doc = true): Elasticsearch|string|array|Collection
    {
        if ($this->isPreventObserver()) {
            if ($this->resolveClient()->isExistsDocument(
                ['id' => $this->model()->id, "index" => $this->getIndexName()]
            )) {
                $response = $this->resolveClient()->indicesUpdateDoc([
                    $this->getIndicator() => $this->model()->id,
                    'index' => $this->getIndexName(),
                    'body' => $use_doc ? ['doc' => $this->getEngineData()] : $this->getEngineData()
                ]);
                $this->setResponse($response);
                return $response;
            } else {
                return $this->createDoc();
            }
        }
        return "Model update is disabled by preventObserver";
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function deleteDoc(int|string $id): array|string
    {
        if ($this->isPreventObserver()) {
            $body = [$this->indicator() => $id, "index" => $this->getIndexName()];
            if ($this->resolveClient()->isExistsDocument($body)) {
                return $this->resolveClient()->documentDelete($this->indexName(), $id);
            }
            return new Collection();
        }
        return "Model delete is disabled by preventObserver";
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(array $dsl): array
    {
        return $this->resolveClient()->search($this->indexName(), $dsl);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function deleteByQuery(array $dsl): array
    {
        return $this->resolveClient()->deleteByQuery($this->indexName(), $dsl);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function isCreated(): bool
    {
        return $this->resolveClient()->indicesExists($this->indexName());
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function create(): void
    {
        $this->resolveClient()->indicesCreate($this->indexName(), $this->settings());
    }

    /**
     * @return array
     */
    public function settings(): array
    {
        throw new \RuntimeException('Need to redefine the method');
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function addToIndex(array $body): Elasticsearch
    {
        return $this->resolveClient()->indicesIndex($body);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function addToUpdate(array $body): Elasticsearch
    {
        return $this->resolveClient()->indicesUpdate($body);
    }


    /**
     * @throws ReflectionException
     */
    private function getMethod($class, string $method): ReflectionMethod
    {
        return new ReflectionMethod($class, $method);
    }

    private function checkParams(object $params, string $instanceof): bool
    {
        return $params instanceof $instanceof;
    }

    /**
     * @param Elasticsearch|Promise|array|Collection $response
     * @return void
     * @throws QueryAdapterException
     */
    private function setResponse(Elasticsearch|Promise|array|Collection $response): void
    {
        //        return $response;
        $status = $response->getStatusCode();
        //        if(($response instanceof ClientResponseException::class) || (ServerResponseException::class)){
        //            $error = new QueryAdapterException(
        //                sprintf("%s %s: %s", $response->getStatusCode(), $response->getReasonPhrase(), (string) $response->getBody()),
        //                $status
        //            );
        //            throw $error->setResponse($response);
        //        }
        //        if(is_callable($this->job)){
        //           $this->runJob();
        //        }
    }
}

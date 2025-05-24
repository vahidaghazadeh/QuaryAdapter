<?php

namespace Opsource\QueryAdapter\Contracts;

use Closure;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Illuminate\Database\Eloquent\Collection;
use Opsource\QueryAdapter\Brokers\BrokerManager;
use Opsource\QueryAdapter\Exceptions\QueryAdapterException;
use Opsource\QueryAdapter\Jobs\ArgsBuilder;

interface AdapterIfc
{
    public function getIndexName(): string;

    /**
     * @return string
     */
    public function getIndicator(): string;


    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function get(int|string|null $id = null): array;

    /**
     * @return Elasticsearch|Promise|string|array|Collection
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws MissingParameterException if a required parameter is missing
     * @throws QueryAdapterException if the status code of response is 5xx
     * @throws ServerResponseException if the status code of response is 5xx
     */
    public function createDoc(): Elasticsearch|Promise|string|array|Collection;

    /**
     * @param  bool  $use_doc
     * @return Elasticsearch|string|array|Collection
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws QueryAdapterException
     * @throws ServerResponseException
     */
    public function updateDoc(bool $use_doc = true): Elasticsearch|string|array|Collection;

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(array $dsl): array;


    public function getJob(): Closure|string;

    public function getBrokerManager(): BrokerManager|null;


    public function getQueueArgs(bool $collapse = false): ArgsBuilder|array;


}

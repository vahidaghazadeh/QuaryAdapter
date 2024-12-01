<?php

namespace Opsource\QueryAdapter\Jobs;

use Elastic\Elasticsearch\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

class IndexDocument implements ShouldQueue
{
    public function __construct(protected Client $client, protected string $index, protected int $id, protected array $params)
    {
    }

    public function handle()
    {
//        \config('query_adapter.')
//        $this->client
    }
}

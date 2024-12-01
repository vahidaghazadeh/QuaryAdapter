<?php

namespace Opsource\QueryAdapter\Jobs;

use App\Performers\RabbitMQDirective;
use Opsource\QueryAdapter\Performers\SearchEngineJobBuilder;

class JobManager
{
    public function __construct(protected SearchEngineJobBuilder $builder)
    {
    }
}

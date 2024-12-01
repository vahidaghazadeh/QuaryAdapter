<?php

namespace Opsource\QueryAdapter\Jobs;

use App\Performers\RabbitMQDirective;
use Opsource\QueryAdapter\Performers\SearchEngineJobBuilder;
use Opsource\QueryAdapter\Traits\JobDecorator;

class BrokerDecorator
{
    use JobDecorator;


    public function __construct(protected SearchEngineJobBuilder $builder)
    {
        $this->setAction($this->builder->getJob());
    }

    public function getQueue(): bool|array
    {
        return $this->builder->parameters->get("queue");
    }


    public function getExchange(): bool|array
    {
        return $this->builder->parameters->get("exchange");
    }
}

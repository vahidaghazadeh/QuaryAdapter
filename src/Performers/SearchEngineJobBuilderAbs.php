<?php

namespace Opsource\QueryAdapter\Performers;

use App\Performers\RabbitMQDirective;
use Opsource\QueryAdapter\Support\Stack;

abstract class SearchEngineJobBuilderAbs
{
    protected RabbitMQDirective $broker;
    public Stack $parameters;
    protected string $queue = 'default';
    protected $job;
    public bool $useCurrentModel = false;
    public ?string $engineDataMethod = null;

    public function __construct($enginJobs)
    {
        $this->job = $enginJobs;
        $this->parameters = new Stack();
    }

    /**
     * @return mixed
     */
    public function getJob()
    {
        return $this->job;
    }

    public function getDataBy(): string
    {
        return $this->engineDataMethod;
    }

    public function setDataBy(string $engineDataMethod): static
    {
        $this->engineDataMethod = $engineDataMethod;
        return $this;
    }

    public function hasUseCurrentModel(): bool
    {
        return $this->useCurrentModel;
    }

    public function setUseCurrentModel(bool $useCurrentModel): static
    {
        $this->engineDataMethod = $useCurrentModel;
        return $this;
    }

    protected function getQueue(): string
    {
        return $this->queue;
    }

    public function setQueue(string $queue): static
    {
        $this->queue = $queue;
        return $this;
    }

    public function getBroker(): RabbitMQDirective
    {
        return $this->broker;
    }

    public function setBroker(RabbitMQDirective $broker): static
    {
        $this->broker = $broker;
        return $this;
    }

    public function pushParameter(array $param): static
    {
        $this->parameters->push($param);
        return $this;
    }

    public function popParameter(): static
    {
        $this->parameters->pop();
        return $this;
    }

    public function peekParameter(): static
    {
        end($this->parameters);
        return $this;
    }
}

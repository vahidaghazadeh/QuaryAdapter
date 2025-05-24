<?php

namespace Opsource\QueryAdapter\Traits;

use Closure;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Opsource\QueryAdapter\Brokers\BrokerManager;
use Opsource\QueryAdapter\Exceptions\QueryAdapterException;
use Opsource\QueryAdapter\Jobs\ArgsBuilder;
use Opsource\QueryAdapter\Jobs\ClosureBuilder;
use Opsource\QueryAdapter\Jobs\JobBuilder;

trait HasUseJob
{
    protected Closure|string $job;
    protected BrokerManager|Closure $brokerManager;
    private ArgsBuilder $queueArgs;

    public function getBrokerManager(): BrokerManager|null
    {
        return BrokerManager::getInstance();
    }

    /**
     * @param  Closure|null  $closure
     * @return QueryAdapterException|$this
     * @throws PhpVersionNotSupportedException
     */
    protected function setBrokerManager(Closure $closure = null): QueryAdapterException|static
    {
        $this->setQueueArgs(['type' => "broker"]);
        if (is_callable($closure)) {
            $serialized = ClosureBuilder::create($closure)->__serialize();
            $this->setQueueArgs(["runner" => $serialized]);
        }

        return $this;
    }

    public function setBuiltinRunner(string $runner): static
    {
        $builtin = $this->getQueueArgs()->search(needle: 'builtin_runner', _global: false);
        if ($builtin->count()) {
            $builtin->push([$runner]);
        } else {
            $this->setQueueArgs(['builtin_runner' => $runner]);
        }
        return $this;
    }

    public function getQueueArgs(bool $collapse = false): ArgsBuilder|array
    {
        if ($collapse) {
            return $this->queueArgs->collapse();
        }
        return $this->queueArgs;
    }


    public function setQueueArgs(array $queueArgs): static|string|array
    {
        $this->queueArgs->push($queueArgs);
        return $this;
    }

    /**
     * @throws PhpVersionNotSupportedException
     */
    public function setJob(string $job, Closure $closure = null): static
    {
        $this->job = $job;
        $this->setQueueArgs(['type' => "builtin"]);
        $this->setQueueArgs(['runner' => "builtin"]);

        if (is_callable($closure)) {
            $this->setQueueArgs(["type" => "closure"]);
            $this->setQueueArgs(["runner" => ClosureBuilder::create(
                $closure
            )->__serialize()]);
        }
        return $this;
    }

    public function getJob(): Closure|string
    {
        return $this->job ?? "";
    }

    /**
     * @throws QueryAdapterException
     * @throws PhpVersionNotSupportedException
     */
    public function runWithJob(bool $use_broker = false): static|QueryAdapterException
    {
        if (!$this->getJob()) {
            return new QueryAdapterException("can't be empty job class");
        }

        $jobBuilder = new JobBuilder($this);

        if ($use_broker) {
            $this->setQueueArgs(['type' => "broker"]);
            $this->setQueueArgs([
                "runner" => ClosureBuilder::create(
                    $this->getBrokerManager()->init(
                        $this->getQueueArgs()->search('message', true, false),
                        $this->getQueueArgs()->search("queue")->first()
                    )->publish()
                )->__serialize()
            ]);
        }

        if (!$use_broker && $this->getQueueArgs()->asTotal()->search('closure') && !$this->getQueueArgs()->count()) {
            $this->setEngineData();
            $this->setQueueArgs($this->getEngineData());
        }

        $jobBuilder->fire();
        return $this;
    }
}

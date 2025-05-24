<?php

namespace Opsource\QueryAdapter\Performers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Exceptions\QueryAdapterException;
use Opsource\QueryAdapter\Traits\InteractsWithIndex;
use ReflectionException;
use ReflectionMethod;

class EngineDirectiveAdapter extends SearchEngine
{
    use InteractsWithIndex;

    public function indicator(): string
    {
        return $this->indicator;
    }

    protected function indexName(): string
    {
        return $this->indexName;
    }

    protected function model(): Model
    {
        return $this->model;
    }

    public function setCustomBuiltEngineData(array $body): static
    {
        $this->engineData = $body;
        return $this;
    }

    public function setEngineData(string $method = null, ...$parameters): static
    {
        $model = $this->model();
        if ($this->isPreventObserver()) {
            if ($method) {
                if (!method_exists($model, $method)) {
                    throw new QueryAdapterException(
                        "The {$method} method does not exist"
                    );
                }
                $reflectionMethod = new ReflectionMethod($model, $method);

                if ($reflectionMethod->isPrivate() || $reflectionMethod->isProtected()) {
                    throw new QueryAdapterException(
                        "The {$method} method is not accessible"
                    );
                }

                $parametersCount = $reflectionMethod->getNumberOfParameters();

                if ($parametersCount > 0) {
                    $this->engineData = $model->{$method}(...$parameters);
                } else {
                    $this->engineData = $model->{$method}();
                }
            } else {
                $this->engineData = $this->model->toArray();
            }
        }
        return $this;
    }

    public function getEngineData(bool $as_collect = false)
    {
        return $as_collect ? collect($this->engineData) : $this->engineData;
    }

    public function dispatch(): static
    {
        //        $this->jobRunner->dispatch($this->getEngineData());
        return $this;
    }
}

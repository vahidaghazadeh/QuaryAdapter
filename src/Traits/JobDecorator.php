<?php

namespace Opsource\QueryAdapter\Traits;

use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Enum\Runner;
use Opsource\QueryAdapter\Jobs\ArgsBuilder;
use Opsource\QueryAdapter\Jobs\ClosureBuilder;
use Opsource\QueryAdapter\Jobs\Decorators\AsBroker;
use Opsource\QueryAdapter\Jobs\Decorators\AsBuiltin;
use Opsource\QueryAdapter\Jobs\Decorators\AsClosure;
use Opsource\QueryAdapter\Performers\SearchEngine;

trait JobDecorator
{
    use AsClosure;
    use AsBroker;
    use AsBuiltin;

    protected ArgsBuilder $args;
    public function __construct(protected SearchEngine $action)
    {
        Log::debug("__construct");
        Log::debug(\json_encode($this->action->getQueueArgs()));
        $this->args = $this->action->getQueueArgs();
    }

    protected function hasTrait(string $trait): bool
    {
        return in_array($trait, class_uses_recursive($this->action->getQueueArgs()->search('trait')));
    }

    protected function hasProperty(string $property): bool
    {
        return property_exists($this->action->getQueueArgs(), $property);
    }

    protected function getProperty(string $property)
    {
        return $this->action->{$this->action->getQueueArgs()->search('property')};
    }

    protected function hasMethod(string $method): bool
    {
        return method_exists($this, $method);
    }

    protected function callMethod(string $method, array $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }

    protected function resolveAndCallMethod(string $method, array $extraArguments = [])
    {
        return app()->call([$this, $method], $extraArguments);
    }

    protected function fromActionMethod(string $method, array $methodParameters = [], $default = null)
    {
        return $this->hasMethod($method)
            ? $this->callMethod($method, $methodParameters)
            : value($default);
    }

    protected function fromActionProperty(string $property, $default = null)
    {
        return $this->hasProperty($property)
            ? $this->getProperty($property)
            : value($default);
    }

    protected function fromActionMethodOrProperty(string $method, string $property, $default = null, array $methodParameters = [])
    {
        if ($this->hasMethod($method)) {
            return $this->callMethod($method, $methodParameters);
        }

        if ($this->hasProperty($property)) {
            return $this->getProperty($property);
        }

        return value($default);
    }

    public function handle()
    {
        Log::debug("HANDLEEEEEEEEEEEEEEEEEEEEEEEE");
        Log::debug(json_encode([
            "Count" => $this->args->asTotal()->search("type"),
            "Runner" => $this->getRunnerKey(),
            "HasMethod" => $this->hasMethod($this->getRunnerKey())
        ]));


        if($this->args->asTotal()->search("type") && $this->hasMethod($this->getRunnerKey())) {
            Log::debug("RUN");
            $this->callMethod(
                $this->getRunnerKey(),
                $this->args->asArray()
            );
        }
    }

    /**
     * @return mixed
     */
    public function getRunner(): ClosureBuilder
    {
        $closure = (new ClosureBuilder($this->args->asValue()->search('runner.closure')))->getClosure();
        return $closure->__unserialize($closure);
    }

    private function getRunnerKey(): string
    {
        return Runner::{strtoupper($this->args->asValue()->search('type'))}->getRunner();
    }
}

<?php

namespace Opsource\QueryAdapter\Facade;

use ReflectionClass;

class InitQueryAdapter
{
    protected static array $aliases = [];

    public static function initialize(): void
    {
        $directives = config('query_adapter.paths.directives');
        if ($directives) {
            foreach ($directives as $directive) {
                if (class_exists($directive)) {
                    $ref = new ReflectionClass($directive);
                    self::$aliases[lcfirst($ref->getShortName())] = $directive;
                }
            }
        }
    }

    public function __call(string $name, array $arguments)
    {
        if (class_exists($name)) {
            self::__callStatic($name, $arguments);
        }
    }

    public static function __callStatic($method, $arguments)
    {
        $method = lcfirst($method);
        if (! isset(self::$aliases[$method])) {
            throw new \BadMethodCallException("Method {$method} does not exist.");
        }

        $engineClass = self::$aliases[$method];

        return new $engineClass(...$arguments);
    }
}

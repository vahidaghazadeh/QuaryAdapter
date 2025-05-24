<?php

namespace Opsource\QueryAdapter\Facade;

use Illuminate\Support\Facades\Facade;
use Modules\Core\Engine\Directives\ProductEngineDirective;

/**
 * Class QueryAdapter
 *
 * @method ProductEngineDirective productQuery($model = null)
 */
class QueryAdapter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'QueryAdapter';
    }
    //    protected static array $aliases = [];
    //
    //    public static function initialize(): void
    //    {
    //        $engines = config("query_adapter.engines");
    //        foreach ($engines as $engine) {
    //            if (class_exists($engine)) {
    //                $ref = new ReflectionClass($engine);
    //                self::$aliases[lcfirst($ref->getShortName())] = $engine;
    //            }
    //        }
    //    }
    //
    //    public function __call(string $name, array $arguments)
    //    {
    //        if (class_exists($name)) {
    //            self::__callStatic($name, $arguments);
    //        }
    //    }
    //
    //    public static function __callStatic($method, $arguments)
    //    {
    //        $method = lcfirst($method);
    //        if (!isset(self::$aliases[$method])) {
    //            throw new \BadMethodCallException("Method {$method} does not exist.");
    //        }
    //
    //        $engineClass = self::$aliases[$method];
    //        return new $engineClass(...$arguments);
    //    }
}

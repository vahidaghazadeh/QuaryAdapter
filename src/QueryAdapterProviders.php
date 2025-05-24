<?php

namespace Opsource\QueryAdapter;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Opsource\QueryAdapter\Brokers\BrokerManager;
use Opsource\QueryAdapter\Client\ElasticClient;
use Opsource\QueryAdapter\Facade\QueryAdapter;
use Opsource\QueryAdapter\Facade\QueryAdapterFacade;
use Opsource\QueryAdapter\Support\StubBuilder;

abstract class QueryAdapterProviders extends ServiceProvider
{
    //    public function boot(): void
    //    {
    //        $this->publishes([
    //            __DIR__.'/config/config.php' => config_path('query_adapter.php'),
    //        ]);
    //        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'query_adapter');
    //        $this->commands([
    //            EngineModelDirectorCommand::class
    //        ]);
    //    }

    public function boot()
    {
    }

    public function register()
    {
    }

    protected function registerNamespaces(): void
    {
        $configPath = __DIR__.'/config/config.php';
        $stubsPath = __DIR__.'/Support/stubs';

        $this->publishes([
            $configPath => config_path('query_adapter.php'),
        ], 'config');

        $this->publishes([
            $stubsPath => base_path('stubs/query-adapter-stubs'),
        ], 'stubs');
    }

    public function setupStubs(): void
    {
        $stubPath = $this->app['config']->get('query_adapter.stubs.path') ?? dirname(__DIR__).'/Support/stubs';
        StubBuilder::setBasePath($stubPath);
    }

    abstract protected function registerServices();

    public function provides()
    {
    }

    public function registerProviders(): void
    {
        //        $engines = config('query_adapter.engines');
        //        if ($engines) {
        //            foreach ($engines as $engine) {
        //                if (class_exists($engine)) {
        //                    $ref = new ReflectionClass($engine);
        //                    $this->app->bind($ref->getShortName(), function () use ($ref, $engine) {
        //                        $class = new $engine();
        //                        $this->app->alias($class, $ref->getShortName());
        //                        return $class;
        //                    });
        //                }
        //            }
        //            QueryAdapter::initialize();
        //        }

        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'query_adapter');

        $this->app->singleton(
            ElasticClient::class,
            fn (Application $app) => ElasticClient::fromConfig($app['config']['query_adapter.connection'])
        );

        $this->app->singleton(BrokerManager::class, BrokerManager::class);


        //        $directives = config('query_adapter.paths.directives');
        //        if ($directives) {
        //            foreach ($directives as $directive) {
        //                if (class_exists($directive)) {
        //                    $ref = new ReflectionClass($directive);
        //                    $this->app->bind($ref->getShortName(), function () use ($directive) {
        //                        return new $directive();
        //                    });
        //                }
        //            }
        //            InitQueryAdapter::initialize();
        //        }

        $this->app->bind("QueryAdapterFacade", QueryAdapterFacade::class);
    }
    //    public function register(): void
    //    {
    //        $engines = config("query_adapter.engines");
    //        if ($engines) {
    //            foreach ($engines as $engine) {
    //                if (class_exists($engine)) {
    //                    $ref = new ReflectionClass($engine);
    //                    $this->app->bind($ref->getShortName(), function () use ($engine) {
    //                        return new $engine();
    //                    });
    //                }
    //            }
    //            QueryAdapter::initialize();
    //        }
    //
    //        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'query_adapter');
    //
    //        $this->app->singleton(
    //            ElasticClient::class,
    //            fn(Application $app) => ElasticClient::fromConfig($app['config']['query_adapter.connection'])
    //        );
    //    }
}

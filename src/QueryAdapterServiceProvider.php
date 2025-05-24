<?php

namespace Opsource\QueryAdapter;

class QueryAdapterServiceProvider extends QueryAdapterProviders
{
    public function boot(): void
    {
        $this->registerNamespaces();
    }

    public function register(): void
    {
        $this->registerServices();
        $this->setupStubs();
        $this->registerProviders();
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'query_adapter');
    }

    protected function registerServices(): void
    {
        $this->app->register(DirectivesServiceProvider::class);
        $this->app->register(CommandsServiceProvider::class);
    }
}

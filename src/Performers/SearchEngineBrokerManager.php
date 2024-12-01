<?php

namespace packages\Opsource\QueryAdapter\src\Performers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use packages\Opsource\QueryAdapter\src\Contracts\SerachEngineBrokerManagerIfc;

class SearchEngineBrokerManager implements SerachEngineBrokerManagerIfc
{

    protected string $config_key;
    protected Container $app;

    /**
     * Configuration repository.
     *
     * @var Repository
     */
    protected Repository $config;

    /**
     * Connection pool.
     *
     * @var Collection
     */
    protected Collection $connections;

    /**
     * Channel pool.
     *
     * @var Collection
     */
    protected Collection $channels;

    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->config = $this->app->get('config');
        $this->connections = new Collection([]);
        $this->channels = new Collection([]);
    }

    public function getConnections(): Collection
    {
        return $this->connections;
    }

    public function getConfig(): Repository
    {
        return $this->config;
    }

    public function getApp(): Container
    {
        return $this->app;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function resolveDefaultConfigName(): string
    {
        $configKey = $this->getConfigKey();
        return $this->config->get("{$configKey}.defaultConnection", 'rabbitmq');
    }

    public function resolveConnection(?string $name = null, $config = null): string
    {
        $name = $name ?? $this->resolveDefaultConfigName();
        if (!$this->connections->has($name)) {
            $this->connections->put(
                $name,
                $this->makeConnection($config ?? $this->resolveConfig($name))
            );
        }

        return $this->connections->get($name);
    }

    public function resolveConfig(string $connectionName)
    {
        return "";
    }

    public function publisher(): string
    {
        return "";
    }

    public function consumer(): string
    {
        return "";
    }

    public function resolveChannelId(): string
    {
        return "";
    }

    public function resolveChannel(): string
    {
        return "";
    }

    public function makeConnection(): string
    {
        return "";
    }

    public function getConfigKey(): string
    {
        return $this->getConfigKey();
    }
    public function setConfigKey(string $configKey): void
    {
        $this->config_key = $configKey;
    }
}
